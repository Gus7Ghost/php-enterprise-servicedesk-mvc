<div class="auth-card">
    <div class="auth-header">
        <span class="auth-logo">🛡️</span>
        <h2>Enterprise Service Desk</h2>
        <p>Control de Incidentes, Soporte y Auditoría ITIL</p>
    </div>

    <form action="/login" method="POST" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required placeholder="admin / agent1 / customer1" autofocus>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
        </div>

        <button type="submit" class="btn btn-primary btn-block">Ingresar al Portal</button>
    </form>

    <div class="demo-accounts">
        <h4>Cuentas de Demostración (1 Clic para Probar):</h4>
        <div class="demo-grid">
            <button type="button" class="btn-demo" onclick="fillDemo('admin', 'AdminPass2026!')">
                👑 <strong>Admin</strong> (admin)
            </button>
            <button type="button" class="btn-demo" onclick="fillDemo('agent1', 'AgentPass2026!')">
                🛠️ <strong>Agente L2</strong> (agent1)
            </button>
            <button type="button" class="btn-demo" onclick="fillDemo('customer1', 'ClientPass2026!')">
                👤 <strong>Cliente</strong> (customer1)
            </button>
        </div>
    </div>
</div>

<script>
function fillDemo(u, p) {
    document.getElementById('username').value = u;
    document.getElementById('password').value = p;
}
</script>
