<div class="page-header">
    <div>
        <h1>Panel de Control Operativo</h1>
        <p class="subtitle">Monitoreo de SLAs, volumen de incidentes y estado de infraestructura</p>
    </div>
    <div>
        <a href="/tickets/create" class="btn btn-primary">+ Reportar Incidente</a>
    </div>
</div>

<div class="metrics-grid">
    <div class="metric-card">
        <span class="metric-icon">📑</span>
        <div class="metric-info">
            <h3>Total Tickets</h3>
            <span class="metric-value"><?= $metrics['total'] ?></span>
        </div>
    </div>
    <div class="metric-card metric-open">
        <span class="metric-icon">🟡</span>
        <div class="metric-info">
            <h3>Abiertos</h3>
            <span class="metric-value"><?= $metrics['open'] ?></span>
        </div>
    </div>
    <div class="metric-card metric-progress">
        <span class="metric-icon">🔵</span>
        <div class="metric-info">
            <h3>En Progreso</h3>
            <span class="metric-value"><?= $metrics['in_progress'] ?></span>
        </div>
    </div>
    <div class="metric-card metric-resolved">
        <span class="metric-icon">🟢</span>
        <div class="metric-info">
            <h3>Resueltos</h3>
            <span class="metric-value"><?= $metrics['resolved'] ?></span>
        </div>
    </div>
    <div class="metric-card metric-critical">
        <span class="metric-icon">🔴</span>
        <div class="metric-info">
            <h3>Críticos Activos</h3>
            <span class="metric-value"><?= $metrics['critical'] ?></span>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2>Incidentes Recientes</h2>
        <a href="/tickets" class="link-view-all">Ver todos los tickets &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentTickets)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">No hay tickets registrados recientemente.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentTickets as $t): ?>
                        <tr>
                            <td><span class="badge-code"><?= htmlspecialchars($t['ticket_code']) ?></span></td>
                            <td><strong><?= htmlspecialchars($t['title']) ?></strong></td>
                            <td><?= htmlspecialchars($t['category']) ?></td>
                            <td><span class="badge badge-priority-<?= htmlspecialchars($t['priority']) ?>"><?= strtoupper(htmlspecialchars($t['priority'])) ?></span></td>
                            <td><span class="badge badge-status-<?= htmlspecialchars($t['status']) ?>"><?= strtoupper(htmlspecialchars($t['status'])) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                            <td>
                                <a href="/tickets/<?= $t['id'] ?>" class="btn-sm btn-outline">Ver Detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
