<?php

declare(strict_types=1);

namespace Tests;

require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
\App\Core\Autoloader::register();

class TestRunner
{
    private static int $passed = 0;
    private static int $failed = 0;

    public static function assert(bool $condition, string $testName): void
    {
        if ($condition) {
            self::$passed++;
            echo "  [PASS] {$testName}\n";
        } else {
            self::$failed++;
            echo "  [FAIL] {$testName}\n";
        }
    }

    public static function assertEquals(mixed $expected, mixed $actual, string $testName): void
    {
        if ($expected === $actual) {
            self::$passed++;
            echo "  [PASS] {$testName}\n";
        } else {
            self::$failed++;
            echo "  [FAIL] {$testName} (Esperado: " . var_export($expected, true) . ", Obtenido: " . var_export($actual, true) . ")\n";
        }
    }

    public static function printSummary(): int
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "RESULTADOS DE PRUEBAS AUTOMATIZADAS (PHP 8.3)\n";
        echo str_repeat("=", 60) . "\n";
        echo "Pruebas Superadas : " . self::$passed . "\n";
        echo "Pruebas Fallidas  : " . self::$failed . "\n";
        echo "Total Ejecutadas  : " . (self::$passed + self::$failed) . "\n";
        echo str_repeat("=", 60) . "\n";

        return self::$failed === 0 ? 0 : 1;
    }
}
