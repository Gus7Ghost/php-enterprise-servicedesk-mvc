<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Security;
use App\Core\Session;

class SecurityTest
{
    public static function run(): void
    {
        echo "\n--> Ejecutando SecurityTest (CSRF, XSS, Hashing)...\n";

        // 1. CSRF Token Generation
        $token = Security::generateCsrfToken();
        TestRunner::assert(!empty($token) && strlen($token) === 64, "Token CSRF generado con entropía criptográfica (64 chars)");

        // 2. CSRF Token Validation
        TestRunner::assert(Security::validateCsrfToken($token), "Validación de token CSRF legítimo debe retornar TRUE");
        TestRunner::assert(!Security::validateCsrfToken("invalid_fake_token_123"), "Token CSRF alterado debe retornar FALSE");
        TestRunner::assert(!Security::validateCsrfToken(null), "Token CSRF nulo debe retornar FALSE");

        // 3. XSS Sanitization
        $malicious = "<script>alert('XSS Attack!');</script>";
        $sanitized = Security::sanitize($malicious);
        TestRunner::assert(!str_contains($sanitized, "<script>"), "Sanitización neutraliza etiquetas <script>");
        TestRunner::assert(str_contains($sanitized, "&lt;script&gt;"), "Sanitización escapa caracteres HTML a entidades seguras");

        // 4. Password Hashing (Bcrypt)
        $rawPassword = "SecretEnterprisePassword2026!";
        $hash = password_hash($rawPassword, PASSWORD_BCRYPT);
        TestRunner::assert(password_verify($rawPassword, $hash), "Verificación de contraseña Bcrypt válida");
        TestRunner::assert(!password_verify("WrongPassword", $hash), "Verificación con contraseña incorrecta es rechazada");
    }
}
