<div class="page-header">
    <div>
        <h1>Registrar Nuevo Incidente de Servicio</h1>
        <p class="subtitle">Complete los detalles para que el equipo de soporte pueda asistirle</p>
    </div>
    <div>
        <a href="/tickets" class="btn btn-outline">&larr; Volver a Tickets</a>
    </div>
</div>

<div class="card form-card mt-3">
    <form action="/tickets" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="title">Título del Incidente / Resumen Breve:</label>
            <input type="text" id="title" name="title" required placeholder="Ej: Pérdida de paquetes en enlace de telemetría" class="form-control">
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="category">Categoría:</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Hardware">Hardware / Sensores IoT</option>
                    <option value="Software">Software / Plataforma</option>
                    <option value="Network">Redes / Telemetría</option>
                    <option value="Security">Seguridad / Certificados</option>
                    <option value="General">Soporte General</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="priority">Prioridad / Impacto:</label>
                <select id="priority" name="priority" class="form-control" required>
                    <option value="low">Baja (Sin impacto operacional directo)</option>
                    <option value="medium" selected>Media (Impacto moderado)</option>
                    <option value="high">Alta (Impacto severo o degradación)</option>
                    <option value="critical">Crítica (Operación detenida / Caída de servicio)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Descripción Detallada del Problema:</label>
            <textarea id="description" name="description" rows="5" required placeholder="Detalle los síntomas, equipos afectados, mensajes de error y pasos previos para reproducir el fallo..." class="form-control"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Registrar y Enviar Ticket</button>
            <a href="/tickets" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
