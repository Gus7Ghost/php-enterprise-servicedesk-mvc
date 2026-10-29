<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

class TicketController extends Controller
{
    private Ticket $ticketModel;
    private Comment $commentModel;
    private User $userModel;

    public function __construct()
    {
        $this->ticketModel = new Ticket();
        $this->commentModel = new Comment();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $user = $this->requireAuth();
        $statusFilter = $_GET['status'] ?? null;
        $tickets = $this->ticketModel->getForUser((int)$user['id'], $user['role'], $statusFilter);

        $this->render('tickets/index', [
            'user' => $user,
            'tickets' => $tickets,
            'currentFilter' => $statusFilter,
        ]);
    }

    public function create(): void
    {
        $user = $this->requireAuth();
        $csrfToken = Security::getCsrfToken();

        $this->render('tickets/create', [
            'user' => $user,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function store(): void
    {
        $user = $this->requireAuth();

        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            $this->redirect('/tickets/create');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $priority = trim($_POST['priority'] ?? 'medium');

        if (empty($title) || empty($description)) {
            Session::setFlash('warning', 'El título y la descripción del incidente son obligatorios.');
            $this->redirect('/tickets/create');
        }

        $ticketId = $this->ticketModel->create([
            'title' => Security::sanitize($title),
            'description' => Security::sanitize($description),
            'category' => Security::sanitize($category),
            'priority' => in_array($priority, ['low', 'medium', 'high', 'critical']) ? $priority : 'medium',
            'customer_id' => (int)$user['id'],
            'agent_id' => null,
        ]);

        Session::setFlash('success', 'Ticket de soporte creado exitosamente.');
        $this->redirect('/tickets/' . $ticketId);
    }

    public function show(string $id): void
    {
        $user = $this->requireAuth();
        $ticketId = (int)$id;

        $ticket = $this->ticketModel->getById($ticketId);
        if (!$ticket) {
            http_response_code(404);
            die("Ticket no encontrado");
        }

        // RBAC: Clientes solo ven sus propios tickets
        if ($user['role'] === 'customer' && (int)$ticket['customer_id'] !== (int)$user['id']) {
            http_response_code(403);
            die("Acceso no autorizado al ticket solicitado");
        }

        $canViewInternal = in_array($user['role'], ['admin', 'agent'], true);
        $comments = $this->commentModel->getForTicket($ticketId, $canViewInternal);
        $agents = ($user['role'] === 'admin') ? $this->userModel->getAllAgents() : [];

        $csrfToken = Security::getCsrfToken();

        $this->render('tickets/show', [
            'user' => $user,
            'ticket' => $ticket,
            'comments' => $comments,
            'agents' => $agents,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function addComment(string $id): void
    {
        $user = $this->requireAuth();
        $ticketId = (int)$id;

        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            $this->redirect('/tickets/' . $ticketId);
        }

        $commentText = trim($_POST['comment'] ?? '');
        $isInternal = isset($_POST['is_internal']) && in_array($user['role'], ['admin', 'agent'], true);

        if (!empty($commentText)) {
            $this->commentModel->create(
                $ticketId,
                (int)$user['id'],
                Security::sanitize($commentText),
                $isInternal
            );
            Session::setFlash('success', 'Comentario añadido al ticket.');
        }

        $this->redirect('/tickets/' . $ticketId);
    }

    public function updateStatus(string $id): void
    {
        $user = $this->requireRole(['admin', 'agent']);
        $ticketId = (int)$id;

        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            $this->redirect('/tickets/' . $ticketId);
        }

        $newStatus = trim($_POST['status'] ?? '');
        if ($this->ticketModel->updateStatus($ticketId, $newStatus)) {
            Session::setFlash('success', "Estado del ticket actualizado a '{$newStatus}'.");
        }

        $this->redirect('/tickets/' . $ticketId);
    }

    public function assignAgent(string $id): void
    {
        $user = $this->requireRole('admin');
        $ticketId = (int)$id;

        $token = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($token)) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            $this->redirect('/tickets/' . $ticketId);
        }

        $agentId = !empty($_POST['agent_id']) ? (int)$_POST['agent_id'] : null;
        $this->ticketModel->assignAgent($ticketId, $agentId);

        Session::setFlash('success', 'Asignación de especialista actualizada.');
        $this->redirect('/tickets/' . $ticketId);
    }
}
