<?php
use App\Core\Session;
$currentUser = Session::get('user');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Service Desk (PHP 8.3 MVC)</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <a href="/dashboard" class="brand">
                <span class="brand-icon">🛡️</span>
                <strong>Enterprise Service Desk</strong>
            </a>
            <nav class="nav-links">
                <a href="/dashboard">Dashboard</a>
                <a href="/tickets">Tickets</a>
                <a href="/tickets/create" class="btn-new">+ Nuevo Incidente</a>
            </nav>
            <div class="user-menu">
                <span class="user-role badge-<?= htmlspecialchars($currentUser['role'] ?? 'guest') ?>">
                    <?= strtoupper(htmlspecialchars($currentUser['role'] ?? 'GUEST')) ?>
                </span>
                <span class="user-name"><?= htmlspecialchars($currentUser['full_name'] ?? 'Usuario') ?></span>
                <a href="/logout" class="btn-logout">Salir</a>
            </div>
        </div>
    </header>

    <main class="container">
        <?php foreach (['success', 'warning', 'danger'] as $type): ?>
            <?php if ($msg = Session::getFlash($type)): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?= $content ?>
    </main>

    <footer class="footer">
        <p>Enterprise Service Desk — Desarrollado por <strong>Gustavo Bryan López Cosme</strong> (UNI FIIS)</p>
    </footer>
</body>
</html>
