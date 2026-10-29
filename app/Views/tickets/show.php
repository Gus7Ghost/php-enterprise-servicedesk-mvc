<div class="page-header">
    <div>
        <span class="badge-code"><?= htmlspecialchars($ticket['ticket_code']) ?></span>
        <h1><?= htmlspecialchars($ticket['title']) ?></h1>
        <p class="subtitle">Creado el <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?> por <?= htmlspecialchars($ticket['customer_name']) ?></p>
    </div>
    <div>
        <a href="/tickets" class="btn btn-outline">&larr; Volver a la lista</a>
    </div>
</div>

<div class="ticket-detail-grid">
    <!-- COLUMNA PRINCIPAL: DETALLE Y TIMELINE -->
    <div class="ticket-main-col">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Descripción del Incidente</h3>
                <span class="badge badge-priority-<?= htmlspecialchars($ticket['priority']) ?>"><?= strtoupper(htmlspecialchars($ticket['priority'])) ?></span>
            </div>
            <div class="card-body">
                <p class="ticket-description"><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
            </div>
        </div>

        <!-- TIMELINE DE COMENTARIOS -->
        <div class="card">
            <div class="card-header">
                <h3>Historial de Comunicaciones y Auditoría</h3>
            </div>
            <div class="card-body">
                <?php if (empty($comments)): ?>
                    <p class="text-muted">No hay comentarios en este ticket aún.</p>
                <?php else: ?>
                    <div class="comments-list">
                        <?php foreach ($comments as $c): ?>
                            <div class="comment-item <?= $c['is_internal'] ? 'comment-internal' : '' ?>">
                                <div class="comment-header">
                                    <strong><?= htmlspecialchars($c['author_name']) ?></strong>
                                    <span class="badge-role"><?= strtoupper(htmlspecialchars($c['author_role'])) ?></span>
                                    <?php if ($c['is_internal']): ?>
                                        <span class="badge-internal">🔒 NOTA INTERNA PRIVADA</span>
                                    <?php endif; ?>
                                    <span class="comment-time"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
                                </div>
                                <div class="comment-body">
                                    <?= nl2br(htmlspecialchars($c['comment'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <!-- FORMULARIO DE RESPUESTA -->
                <form action="/tickets/<?= $ticket['id'] ?>/comments" method="POST" class="reply-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    
                    <div class="form-group">
                        <label for="comment"><strong>Añadir Respuesta o Actualización:</strong></label>
                        <textarea id="comment" name="comment" rows="3" required placeholder="Escriba su respuesta..." class="form-control"></textarea>
                    </div>

                    <?php if (in_array($user['role'], ['admin', 'agent'], true)): ?>
                        <div class="form-check mb-3">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_internal" value="1">
                                <strong>Marcar como Nota Interna</strong> (Solo visible para Agentes y Administradores, oculta al cliente)
                            </label>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">Publicar Respuesta</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SIDEBAR: ESTADO, ASIGNACIÓN Y METADATOS -->
    <div class="ticket-side-col">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Detalles de Gestión</h4>
            </div>
            <div class="card-body">
                <div class="meta-item">
                    <span class="meta-label">Estado Actual:</span>
                    <span class="badge badge-status-<?= htmlspecialchars($ticket['status']) ?>"><?= strtoupper(htmlspecialchars($ticket['status'])) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Categoría:</span>
                    <strong><?= htmlspecialchars($ticket['category']) ?></strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Especialista Asignado:</span>
                    <strong><?= htmlspecialchars($ticket['agent_name'] ?? 'Sin asignar') ?></strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Solicitante:</span>
                    <div>
                        <strong><?= htmlspecialchars($ticket['customer_name']) ?></strong>
                        <div class="text-sm text-muted"><?= htmlspecialchars($ticket['customer_email']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GESTIÓN DE ESTADO (SOLO AGENTES Y ADMINS) -->
        <?php if (in_array($user['role'], ['admin', 'agent'], true)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Actualizar Estado (SLA)</h4>
                </div>
                <div class="card-body">
                    <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <div class="form-group">
                            <select name="status" class="form-control mb-2">
                                <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Abierto</option>
                                <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>En Progreso</option>
                                <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Resuelto</option>
                                <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline btn-block">Guardar Estado</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- ASIGNACIÓN DE AGENTE (SOLO ADMINS) -->
        <?php if ($user['role'] === 'admin'): ?>
            <div class="card">
                <div class="card-header">
                    <h4>Asignar Especialista</h4>
                </div>
                <div class="card-body">
                    <form action="/tickets/<?= $ticket['id'] ?>/assign" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <div class="form-group">
                            <select name="agent_id" class="form-control mb-2">
                                <option value="">-- Sin asignar --</option>
                                <?php foreach ($agents as $ag): ?>
                                    <option value="<?= $ag['id'] ?>" <?= (int)($ticket['agent_id'] ?? 0) === (int)$ag['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ag['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline btn-block">Reasignar Ticket</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
