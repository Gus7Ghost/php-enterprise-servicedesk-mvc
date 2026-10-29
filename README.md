# Enterprise Service Desk & ITIL Incident Portal (PHP 8.3 MVC)

[![PHP](https://img.shields.io/badge/PHP-8.3%20Native-777bb4.svg)](https://www.php.net/)
[![Architecture](https://img.shields.io/badge/Architecture-MVC%20%2F%20Front%20Controller-blue.svg)](#-arquitectura-del-sistema-mvc)
[![Security](https://img.shields.io/badge/Security-RBAC%20%7C%20CSRF%20%7C%20PDO-darkgreen.svg)](#-mecanismos-de-seguridad-enterprise)
[![Tests](https://img.shields.io/badge/Tests-20%2F20%20Passed-brightgreen.svg)](#-suite-de-pruebas-automatizadas)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Portal corporativo para la gestión de incidentes de soporte tecnológico, SLAs y atención al cliente desarrollado en **PHP 8.3 nativo puro** bajo el patrón arquitectónico **Model-View-Controller (MVC)** y **Front Controller**. 

Diseñado sin dependencias de frameworks externos para evidenciar un dominio profundo de los fundamentos del lenguaje, el ciclo de vida de peticiones HTTP, seguridad web defensiva y separación de responsabilidades.

---

## 📌 Contexto y Desafíos de Negocio

En operaciones de infraestructura tecnológica y centros de monitoreo (como los laboratorios de cómputo y flotas de robótica en **LabIAR - UNI**), es crítico gestionar solicitudes de servicio con estricto control de acceso y trazabilidad de comunicaciones.

### Capacidades implementadas:
1. **Control de Acceso Basado en Roles (RBAC):**
   - **Administrador:** Acceso irrestricto, asignación de tickets a especialistas, auditoría y métricas globales.
   - **Agente de Soporte (L2):** Atención de tickets, actualización de estados de ciclo de vida (SLA) y redacción de **Notas Internas Confidenciales** (invisibles para el solicitante).
   - **Cliente:** Reporte de incidentes y seguimiento restringido exclusivamente a sus propios tickets.
2. **Ciclo de vida y SLAs:** Transiciones auditables (`open` &rarr; `in_progress` &rarr; `resolved` &rarr; `closed`).
3. **Métricas en Tiempo Real:** Dashboard ejecutivo con conteo de incidentes críticos, tiempos de respuesta y carga de trabajo por especialista.

---

## 🏛️ Arquitectura del Sistema (MVC)

El proyecto implementa una estructura desacoplada con enrutador frontal y autoloader PSR-4:

```
portafolio/10-php-enterprise-servicedesk-mvc/
├── app/
│   ├── Config/
│   │   └── Database.php          # Singleton PDO con integridad referencial SQLite
│   ├── Controllers/
│   │   ├── AuthController.php    # Autenticación, control de sesiones y regeneración de ID
│   │   ├── DashboardController.php# Agregación de KPIs y vista de incidentes recientes
│   │   └── TicketController.php  # CRUD de incidentes, validación CSRF y control RBAC
│   ├── Core/
│   │   ├── Autoloader.php        # Autocargador PSR-4 nativo sin dependencias externas
│   │   ├── Controller.php        # Controlador base con renderizado de vistas y layouts
│   │   ├── Model.php             # Modelo base con inyección de conexión PDO
│   │   ├── Router.php            # Enrutador Front Controller con soporte para URLs limpias
│   │   ├── Security.php          # Generación/validación de tokens CSRF y sanitización XSS
│   │   └── Session.php           # Gestor seguro de sesión y mensajes flash
│   ├── Models/
│   │   ├── User.php              # Modelo de usuarios y verificación Bcrypt
│   │   ├── Ticket.php            # Modelo de incidentes, consultas parametrizadas y métricas
│   │   └── Comment.php           # Historial de comunicaciones con bandera de confidencialidad
│   └── Views/
│       ├── layouts/              # Layout principal semántico y layout de autenticación
│       ├── auth/login.php        # Formulario de acceso con botones de demo de 1 clic
│       ├── dashboard/index.php   # Panel de métricas e indicadores de rendimiento
│       └── tickets/              # Vistas de listado, creación y detalle con timeline
│
├── database/
│   ├── schema.sql                # DDL con claves foráneas e índices optimizados
│   ├── seed.php                  # Sembrador de datos iniciales y cuentas de demostración
│   └── servicedesk.sqlite        # Base de datos relacional SQLite
│
├── public/                       # Raíz web pública (aislamiento del código fuente)
│   ├── index.php                 # Front Controller único
│   └── css/style.css             # Estilos responsivos modernos sin librerías externas
│
└── tests/
    ├── TestRunner.php            # Motor de pruebas unitarias y de integración nativo
    ├── SecurityTest.php          # Pruebas de tokens CSRF, sanitización XSS y hashing Bcrypt
    ├── RbacTest.php              # Verificación de aislamiento de datos y notas internas
    ├── TicketLifecycleTest.php   # Pruebas de máquina de estados y transiciones de tickets
    └── run_all.php               # Ejecutor unificado de la suite de pruebas
```

---

## 🛡️ Mecanismos de Seguridad Enterprise

1. **Protección contra Inyección SQL (100% PDO Prepared Statements):**  
   Todas las consultas a la base de datos se ejecutan con *prepared statements* y vinculación de parámetros con tipado estricto. La emulación de consultas preparadas está desactivada a nivel de driver (`PDO::ATTR_EMULATE_PREPARES => false`).
2. **Defensa contra Cross-Site Request Forgery (Anti-CSRF):**  
   Tokens criptográficos pseudoaleatorios generados con `random_bytes(32)` y validados en tiempo constante con `hash_equals()` en todas las peticiones con mutación de estado (`POST`).
3. **Prevención de Cross-Site Scripting (XSS):**  
   Sanitización estricta de entradas y salidas mediante `htmlspecialchars($input, ENT_QUOTES, 'UTF-8')`.
4. **Protección de Sesiones:**  
   Regeneración obligatoria del identificador de sesión (`session_regenerate_id(true)`) tras una autenticación exitosa para frustrar ataques de *Session Fixation*. Cookies de sesión configuradas con flags `HttpOnly` y `SameSite=Lax`.
5. **Cifrado de Contraseñas:**  
   Almacenamiento de credenciales procesadas con el algoritmo estándar de la industria **Bcrypt** (`PASSWORD_BCRYPT`).

---

## 🧪 Suite de Pruebas Automatizadas

El proyecto incluye una suite automatizada nativa de 20 pruebas unitarias y de integración:

```bash
php tests/run_all.php
```

### Resultado de la Ejecución Verificada:
```text
============================================================
SUITE DE PRUEBAS AUTOMATIZADAS - ENTERPRISE SERVICE DESK
============================================================

--> Ejecutando SecurityTest (CSRF, XSS, Hashing)...
  [PASS] Token CSRF generado con entropía criptográfica (64 chars)
  [PASS] Validación de token CSRF legítimo debe retornar TRUE
  [PASS] Token CSRF alterado debe retornar FALSE
  [PASS] Token CSRF nulo debe retornar FALSE
  [PASS] Sanitización neutraliza etiquetas <script>
  [PASS] Sanitización escapa caracteres HTML a entidades seguras
  [PASS] Verificación de contraseña Bcrypt válida
  [PASS] Verificación con contraseña incorrecta es rechazada

--> Ejecutando RbacTest (Aislamiento de Clientes y Permisos)...
  [PASS] Aislamiento: Cliente solo consulta tickets donde es propietario
  [PASS] Visibilidad: Administrador consulta universo completo de incidentes
  [PASS] Confidencialidad: Vista pública del cliente no filtra notas internas
  [PASS] Soporte L2: Vista de agentes/admins incluye notas internas operacionales

--> Ejecutando TicketLifecycleTest (Ciclo de Vida y Transiciones)...
  [PASS] Creación de ticket genera ID autoincremental positivo
  [PASS] Código de ticket sigue formato estándar TK-YYYY-XXXX
  [PASS] Estado inicial de nuevo ticket es siempre 'open'
  [PASS] Transición a estado 'in_progress' ejecutada con éxito
  [PASS] Estado persistido en base de datos es 'in_progress'
  [PASS] Rechazo de estado inválido preserva invariante de base de datos
  [PASS] Asignación de especialista técnico completada
  [PASS] ID de agente asignado coincide con especialista L2

============================================================
RESULTADOS DE PRUEBAS AUTOMATIZADAS (PHP 8.3)
============================================================
Pruebas Superadas : 20
Pruebas Fallidas  : 0
Total Ejecutadas  : 20
============================================================
```

---

## 💻 Instrucciones de Instalación y Demostración

### Prerrequisitos:
- PHP 8.2 o superior (con extensiones `pdo_sqlite` habilitadas).

### 1. Clonar el repositorio
```bash
git clone https://github.com/Gus7Ghost/php-enterprise-servicedesk-mvc.git
cd php-enterprise-servicedesk-mvc
```

### 2. Poblar la base de datos de demostración
```bash
php database/seed.php
```

### 3. Iniciar el servidor web incorporado de PHP
```bash
php -S localhost:8000 -t public
```

### 4. Acceder al sistema en su navegador:
Abra `http://localhost:8000` en su navegador. La pantalla de login incluye **botones de 1 clic** para ingresar de inmediato con cualquiera de las siguientes cuentas:
- **Administrador:** `admin` / `AdminPass2026!`
- **Agente Soporte L2:** `agent1` / `AgentPass2026!`
- **Cliente:** `customer1` / `ClientPass2026!`

---

## 👤 Autor

**Gustavo Bryan López Cosme**  
- **Rol:** Estudiante de 9no ciclo de Ingeniería de Sistemas — Universidad Nacional de Ingeniería (UNI, Lima, Perú)
- **Investigador:** LabIAR (Laboratorio de Inteligencia Artificial y Robótica - UNI)
- **Email:** [glopezc@uni.pe](mailto:glopezc@uni.pe)
- **GitHub:** [@Gus7Ghost](https://github.com/Gus7Ghost)
- **LinkedIn:** [Perfil en LinkedIn](https://www.linkedin.com)
