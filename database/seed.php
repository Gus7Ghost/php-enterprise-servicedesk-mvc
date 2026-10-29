<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
\App\Core\Autoloader::register();

use App\Config\Database;

$db = Database::getConnection();

echo "==> Inicializando esquema de base de datos...\n";
$schema = file_get_contents(__DIR__ . '/schema.sql');
$db->exec($schema);

echo "==> Limpiando datos anteriores...\n";
$db->exec("DELETE FROM ticket_comments;");
$db->exec("DELETE FROM tickets;");
$db->exec("DELETE FROM users;");
$db->exec("DELETE FROM audit_logs;");

echo "==> Insertando usuarios demo...\n";
$users = [
    [
        'username' => 'admin',
        'email' => 'admin@enterprise-sd.pe',
        'password' => password_hash('AdminPass2026!', PASSWORD_BCRYPT),
        'role' => 'admin',
        'full_name' => 'Ing. Gustavo López (Admin TI)'
    ],
    [
        'username' => 'agent1',
        'email' => 'cmendoza@enterprise-sd.pe',
        'password' => password_hash('AgentPass2026!', PASSWORD_BCRYPT),
        'role' => 'agent',
        'full_name' => 'Carlos Mendoza (Especialista L2)'
    ],
    [
        'username' => 'customer1',
        'email' => 'cliente@corporacion.pe',
        'password' => password_hash('ClientPass2026!', PASSWORD_BCRYPT),
        'role' => 'customer',
        'full_name' => 'Mariana Rivas (Gerente de Operaciones)'
    ]
];

$stmtUser = $db->prepare("INSERT INTO users (username, email, password_hash, role, full_name) VALUES (:username, :email, :password, :role, :full_name)");
foreach ($users as $u) {
    $stmtUser->execute([
        ':username' => $u['username'],
        ':email' => $u['email'],
        ':password' => $u['password'],
        ':role' => $u['role'],
        ':full_name' => $u['full_name'],
    ]);
}

$adminId = 1;
$agentId = 2;
$customerId = 3;

echo "==> Insertando tickets de prueba...\n";
$tickets = [
    [
        'ticket_code' => 'TK-2026-1001',
        'title' => 'Caída de enlace VPN entre sede central y planta de drones',
        'description' => 'El enlace túnel IPsec presenta desconexiones intermitentes cada 15 minutos, afectando la telemetría en tiempo real.',
        'category' => 'Network',
        'priority' => 'critical',
        'status' => 'in_progress',
        'customer_id' => $customerId,
        'agent_id' => $agentId,
    ],
    [
        'ticket_code' => 'TK-2026-1002',
        'title' => 'Renovación de certificados TLS/SSL en cluster de APIs',
        'description' => 'Los certificados comodín *.labiar.uni.pe expiran en 5 días. Se requiere actualización del bundle.',
        'category' => 'Security',
        'priority' => 'high',
        'status' => 'open',
        'customer_id' => $customerId,
        'agent_id' => null,
    ],
    [
        'ticket_code' => 'TK-2026-1003',
        'title' => 'Calibración de telemetría de turbinas térmicas',
        'description' => 'Sensores de temperatura SENS-TEMP-101 reportaron picos superiores a 85°C. Solicito verificación de firmware.',
        'category' => 'Hardware',
        'priority' => 'medium',
        'status' => 'resolved',
        'customer_id' => $customerId,
        'agent_id' => $agentId,
    ]
];

$stmtTicket = $db->prepare("INSERT INTO tickets (ticket_code, title, description, category, priority, status, customer_id, agent_id) VALUES (:ticket_code, :title, :description, :category, :priority, :status, :customer_id, :agent_id)");
foreach ($tickets as $t) {
    $stmtTicket->execute([
        ':ticket_code' => $t['ticket_code'],
        ':title' => $t['title'],
        ':description' => $t['description'],
        ':category' => $t['category'],
        ':priority' => $t['priority'],
        ':status' => $t['status'],
        ':customer_id' => $t['customer_id'],
        ':agent_id' => $t['agent_id'],
    ]);
}

echo "==> Insertando comentarios y notas internas...\n";
$comments = [
    [
        'ticket_id' => 1,
        'user_id' => $agentId,
        'comment' => 'Hemos detectado congestión por paquetes MTU fragmentados. Se está ajustando el MSS a 1360.',
        'is_internal' => 0
    ],
    [
        'ticket_id' => 1,
        'user_id' => $agentId,
        'comment' => 'NOTA INTERNA: El switch perimetral requiere reboot programado a medianoche.',
        'is_internal' => 1 // Nota privada solo para agentes/admins
    ],
    [
        'ticket_id' => 1,
        'user_id' => $customerId,
        'comment' => 'Recibido, quedamos atentos al corte de ventana.',
        'is_internal' => 0
    ]
];

$stmtComment = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment, is_internal) VALUES (:ticket_id, :user_id, :comment, :is_internal)");
foreach ($comments as $c) {
    $stmtComment->execute([
        ':ticket_id' => $c['ticket_id'],
        ':user_id' => $c['user_id'],
        ':comment' => $c['comment'],
        ':is_internal' => $c['is_internal'],
    ]);
}

echo "==> Base de datos poblada exitosamente.\n";
