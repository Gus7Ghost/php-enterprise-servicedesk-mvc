<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ticket;

class DashboardController extends Controller
{
    private Ticket $ticketModel;

    public function __construct()
    {
        $this->ticketModel = new Ticket();
    }

    public function index(): void
    {
        $user = $this->requireAuth();
        $metrics = $this->ticketModel->getMetrics((int)$user['id'], $user['role']);
        $recentTickets = $this->ticketModel->getForUser((int)$user['id'], $user['role']);

        $this->render('dashboard/index', [
            'user' => $user,
            'metrics' => $metrics,
            'recentTickets' => array_slice($recentTickets, 0, 5),
        ]);
    }
}
