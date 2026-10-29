<div class="page-header">
    <div>
        <h1>Mesa de Ayuda &amp; Tickets</h1>
        <p class="subtitle">Gestión centralizada de incidentes y solicitudes de servicio</p>
    </div>
    <div>
        <a href="/tickets/create" class="btn btn-primary">+ Nuevo Incidente</a>
    </div>
</div>

<div class="filter-bar">
    <span class="filter-label">Filtrar por estado:</span>
    <a href="/tickets" class="filter-chip <?= empty($currentFilter) ? 'active' : '' ?>">Todos</a>
    <a href="/tickets?status=open" class="filter-chip <?= $currentFilter === 'open' ? 'active' : '' ?>">Abiertos</a>
    <a href="/tickets?status=in_progress" class="filter-chip <?= $currentFilter === 'in_progress' ? 'active' : '' ?>">En Progreso</a>
    <a href="/tickets?status=resolved" class="filter-chip <?= $currentFilter === 'resolved' ? 'active' : '' ?>">Resueltos</a>
    <a href="/tickets?status=closed" class="filter-chip <?= $currentFilter === 'closed' ? 'active' : '' ?>">Cerrados</a>
</div>

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Solicitante</th>
                    <th>Categoría</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Asignado a</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">No se encontraron tickets con el criterio seleccionado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><span class="badge-code"><?= htmlspecialchars($t['ticket_code']) ?></span></td>
                            <td><strong><?= htmlspecialchars($t['title']) ?></strong></td>
                            <td><?= htmlspecialchars($t['customer_name'] ?? 'Cliente') ?></td>
                            <td><?= htmlspecialchars($t['category']) ?></td>
                            <td><span class="badge badge-priority-<?= htmlspecialchars($t['priority']) ?>"><?= strtoupper(htmlspecialchars($t['priority'])) ?></span></td>
                            <td><span class="badge badge-status-<?= htmlspecialchars($t['status']) ?>"><?= strtoupper(htmlspecialchars($t['status'])) ?></span></td>
                            <td><?= htmlspecialchars($t['agent_name'] ?? 'Sin asignar') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                            <td>
                                <a href="/tickets/<?= $t['id'] ?>" class="btn-sm btn-outline">Abrir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
