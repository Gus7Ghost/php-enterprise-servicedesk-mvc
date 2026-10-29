<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Ticket extends Model
{
    public function getForUser(int $userId, string $role, ?string $statusFilter = null): array
    {
        $sql = "SELECT t.*, 
                       c.full_name as customer_name, c.email as customer_email,
                       a.full_name as agent_name
                FROM tickets t
                JOIN users c ON t.customer_id = c.id
                LEFT JOIN users a ON t.agent_id = a.id
                WHERE 1=1";

        $params = [];

        // Si es cliente, solo ve sus propios tickets
        if ($role === 'customer') {
            $sql .= " AND t.customer_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if (!empty($statusFilter) && in_array($statusFilter, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $statusFilter;
        }

        $sql .= " ORDER BY CASE t.priority 
                    WHEN 'critical' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'medium' THEN 3 
                    WHEN 'low' THEN 4 
                    ELSE 5 END, t.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT t.*, 
                                           c.full_name as customer_name, c.email as customer_email,
                                           a.full_name as agent_name, a.email as agent_email
                                    FROM tickets t
                                    JOIN users c ON t.customer_id = c.id
                                    LEFT JOIN users a ON t.agent_id = a.id
                                    WHERE t.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $ticket = $stmt->fetch();
        return $ticket ?: null;
    }

    public function create(array $data): int
    {
        // Generar código único de ticket TK-AÑO-RAND
        $ticketCode = 'TK-' . date('Y') . '-' . str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        $stmt = $this->db->prepare("INSERT INTO tickets 
            (ticket_code, title, description, category, priority, status, customer_id, agent_id) 
            VALUES (:ticket_code, :title, :description, :category, :priority, 'open', :customer_id, :agent_id)");

        $stmt->execute([
            ':ticket_code' => $ticketCode,
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':priority' => $data['priority'],
            ':customer_id' => $data['customer_id'],
            ':agent_id' => $data['agent_id'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $validStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        if (!in_array($status, $validStatuses, true)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE tickets SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function assignAgent(int $id, ?int $agentId): bool
    {
        $stmt = $this->db->prepare("UPDATE tickets SET agent_id = :agent_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([':agent_id' => $agentId, ':id' => $id]);
    }

    public function getMetrics(?int $userId = null, string $role = 'admin'): array
    {
        $baseSql = "FROM tickets WHERE 1=1";
        $params = [];

        if ($role === 'customer' && $userId !== null) {
            $baseSql .= " AND customer_id = :user_id";
            $params[':user_id'] = $userId;
        }

        $stmt = $this->db->prepare("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
            SUM(CASE WHEN priority = 'critical' AND status != 'closed' THEN 1 ELSE 0 END) as critical_count
            {$baseSql}");

        $stmt->execute($params);
        $res = $stmt->fetch();

        return [
            'total' => (int)($res['total'] ?? 0),
            'open' => (int)($res['open_count'] ?? 0),
            'in_progress' => (int)($res['in_progress_count'] ?? 0),
            'resolved' => (int)($res['resolved_count'] ?? 0),
            'critical' => (int)($res['critical_count'] ?? 0),
        ];
    }
}
