<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);

        // Capturar contenido de la vista hija
        ob_start();
        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";
        if (!file_exists($viewFile)) {
            die("Error: Vista no encontrada: {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Renderizar dentro del layout
        $layoutFile = dirname(__DIR__) . "/Views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function getCurrentUser(): ?array
    {
        return Session::get('user');
    }

    protected function requireAuth(): array
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            Session::setFlash('warning', 'Debe iniciar sesión para acceder al sistema.');
            $this->redirect('/login');
        }
        return $user;
    }

    protected function requireRole(string|array $allowedRoles): array
    {
        $user = $this->requireAuth();
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

        if (!in_array($user['role'], $roles, true)) {
            http_response_code(403);
            die("<h1 style='text-align:center; font-family:sans-serif; margin-top:50px; color:#c0392b;'>403 - Acceso Denegado: Permisos Insuficientes</h1>");
        }

        return $user;
    }
}
