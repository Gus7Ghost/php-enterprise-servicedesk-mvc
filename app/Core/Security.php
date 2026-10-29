<?php

declare(strict_types=1);

namespace App\Core;

class Security
{
    public static function generateCsrfToken(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function getCsrfToken(): string
    {
        Session::start();
        $token = Session::get('csrf_token');
        if (!$token) {
            $token = self::generateCsrfToken();
        }
        return $token;
    }

    public static function validateCsrfToken(?string $token): bool
    {
        Session::start();
        $stored = Session::get('csrf_token');
        if (empty($token) || empty($stored)) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
