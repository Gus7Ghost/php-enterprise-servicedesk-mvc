<?php

declare(strict_types=1);

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/SecurityTest.php';
require_once __DIR__ . '/RbacTest.php';
require_once __DIR__ . '/TicketLifecycleTest.php';

use Tests\RbacTest;
use Tests\SecurityTest;
use Tests\TestRunner;
use Tests\TicketLifecycleTest;

echo "============================================================\n";
echo "SUITE DE PRUEBAS AUTOMATIZADAS - ENTERPRISE SERVICE DESK\n";
echo "============================================================\n";

SecurityTest::run();
RbacTest::run();
TicketLifecycleTest::run();

$exitCode = TestRunner::printSummary();
exit($exitCode);
