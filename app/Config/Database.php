<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static ?string $customPath = null;

    public static function setCustomPath(?string $path): void
    {
        self::$customPath = $path;
        self::$instance = null; // Reiniciar instancia con nueva ruta
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dbPath = self::$customPath ?? dirname(__DIR__, 2) . '/database/servicedesk.sqlite';
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            try {
                self::$instance = new PDO("sqlite:{$dbPath}", null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                // Habilitar integridad referencial en SQLite
                self::$instance->exec('PRAGMA foreign_keys = ON;');
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
