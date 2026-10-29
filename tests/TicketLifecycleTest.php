<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Ticket;

class TicketLifecycleTest
{
    public static function run(): void
    {
        echo "\n--> Ejecutando TicketLifecycleTest (Ciclo de Vida y Transiciones)...\n";

        $ticketModel = new Ticket();

        // 1. Creación de ticket con código formateado
        $newId = $ticketModel->create([
            'title' => 'Incidente de prueba automatizada',
            'description' => 'Fallo en la sincronización de telemetría de drones',
            'category' => 'Network',
            'priority' => 'critical',
            'customer_id' => 3,
            'agent_id' => null,
        ]);

        TestRunner::assert($newId > 0, "Creación de ticket genera ID autoincremental positivo");

        $created = $ticketModel->getById($newId);
        TestRunner::assert(!empty($created['ticket_code']) && str_starts_with($created['ticket_code'], 'TK-'), "Código de ticket sigue formato estándar TK-YYYY-XXXX");
        TestRunner::assertEquals('open', $created['status'], "Estado inicial de nuevo ticket es siempre 'open'");

        // 2. Actualización de estado válida
        $updated = $ticketModel->updateStatus($newId, 'in_progress');
        TestRunner::assert($updated, "Transición a estado 'in_progress' ejecutada con éxito");

        $ticketAfter = $ticketModel->getById($newId);
        TestRunner::assertEquals('in_progress', $ticketAfter['status'], "Estado persistido en base de datos es 'in_progress'");

        // 3. Rechazo de estado inválido
        $invalidUpdate = $ticketModel->updateStatus($newId, 'non_existent_status');
        TestRunner::assert(!$invalidUpdate, "Rechazo de estado inválido preserva invariante de base de datos");

        // 4. Asignación de agente especialista
        $assigned = $ticketModel->assignAgent($newId, 2);
        TestRunner::assert($assigned, "Asignación de especialista técnico completada");

        $assignedTicket = $ticketModel->getById($newId);
        TestRunner::assertEquals(2, (int)$assignedTicket['agent_id'], "ID de agente asignado coincide con especialista L2");
    }
}
