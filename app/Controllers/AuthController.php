<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if ($this->getCurrentUser()) {
            $this->redirect('/dashboard');
        }

        $csrfToken = Security::getCsrfToken();
        $this->render('auth/login', ['csrfToken' => $csrfToken], 'auth');
    }

    public function login(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            Session::setFlash('danger', 'Token CSRF inválido o expirado.');
            $this->redirect('/login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Session::setFlash('warning', 'Ingrese su usuario y contraseña.');
            $this->redirect('/login');
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            Session::setFlash('danger', 'Credenciales incorrectas. Verifique usuario o contraseña.');
            $this->redirect('/login');
        }

        // Regenerar ID de sesión para prevenir Session Fixation
        Session::regenerate();

        // Almacenar usuario autenticado (excluyendo hash)
        Session::set('user', [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
        ]);

        Session::setFlash('success', "Bienvenido al sistema, {$user['full_name']}.");
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/login');
    }
}
