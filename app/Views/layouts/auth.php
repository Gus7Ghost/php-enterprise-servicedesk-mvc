<?php
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Enterprise Service Desk</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <?php foreach (['success', 'warning', 'danger'] as $type): ?>
            <?php if ($msg = Session::getFlash($type)): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?= $content ?>
    </div>
</body>
</html>
