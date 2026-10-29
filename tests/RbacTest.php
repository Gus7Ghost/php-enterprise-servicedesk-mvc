<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

class RbacTest
{
    public static function run(): void
    {
        echo "\n--> Ejecutando RbacTest (Aislamiento de Clientes y Permisos)...\n";

        $ticketModel = new Ticket();
        $commentModel = new Comment();

        // 1. Cliente (customer1 ID: 3) solo ve sus tickets
        $customerTickets = $ticketModel->getForUser(3, 'customer');
        $allBelongToCustomer = true;
        foreach ($customerTickets as $t) {
            if ((int)$t['customer_id'] !== 3) {
                $allBelongToCustomer = false;
                break;
            }
        }
        TestRunner::assert($allBelongToCustomer && count($customerTickets) > 0, "Aislamiento: Cliente solo consulta tickets donde es propietario");

        // 2. Administrador o Agente ve todos los tickets sin filtrar por ID
        $adminTickets = $ticketModel->getForUser(1, 'admin');
        TestRunner::assert(count($adminTickets) >= count($customerTickets), "Visibilidad: Administrador consulta universo completo de incidentes");

        // 3. Notas Internas: Ocultas para clientes
        $publicComments = $commentModel->getForTicket(1, false);
        $hasInternalInPublic = false;
        foreach ($publicComments as $c) {
            if ((int)$c['is_internal'] === 1) {
                $hasInternalInPublic = true;
                break;
            }
        }
        TestRunner::assert(!$hasInternalInPublic, "Confidencialidad: Vista pública del cliente no filtra notas internas");

        // 4. Notas Internas: Visibles para personal de soporte
        $staffComments = $commentModel->getForTicket(1, true);
        $hasInternalInStaff = false;
        foreach ($staffComments as $c) {
            if ((int)$c['is_internal'] === 1) {
                $hasInternalInStaff = true;
                break;
            }
        }
        TestRunner::assert($hasInternalInStaff, "Soporte L2: Vista de agentes/admins incluye notas internas operacionales");
    }
}
