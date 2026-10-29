<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Comment extends Model
{
    public function getForTicket(int $ticketId, bool $includeInternal = false): array
    {
        $sql = "SELECT c.*, u.full_name as author_name, u.role as author_role
                FROM ticket_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.ticket_id = :ticket_id";

        if (!$includeInternal) {
            $sql .= " AND c.is_internal = 0";
        }

        $sql .= " ORDER BY c.created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
        return $stmt->fetchAll();
    }

    public function create(int $ticketId, int $userId, string $comment, bool $isInternal = false): bool
    {
        $stmt = $this->db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment, is_internal) 
                                    VALUES (:ticket_id, :user_id, :comment, :is_internal)");

        return $stmt->execute([
            ':ticket_id' => $ticketId,
            ':user_id' => $userId,
            ':comment' => $comment,
            ':is_internal' => $isInternal ? 1 : 0,
        ]);
    }
}
