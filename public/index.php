<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\MiembroController;
use Controllers\APImiembros;
use Controllers\APImovimientos;
use Controllers\APIsaldos;
use Controllers\APIconceptos;
use Controllers\APItipos;
use Controllers\APIgrupos;
use Controllers\APIdeudas;
$router = new Router();

// Iniciar sesión
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Crear cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

// Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// Recuperar password
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);
$router->get('/recuperar', [LoginController::class, 'recuperar']);
$router->post('/recuperar', [LoginController::class, 'recuperar']);

// Dashboard de grupos
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/crear-grupo', [DashboardController::class, 'crear']);
$router->post('/crear-grupo', [DashboardController::class, 'crear']);
$router->get('/modificar-grupo', [DashboardController::class, 'modificar']);
$router->post('/modificar-grupo', [DashboardController::class, 'modificar']);
$router->get('/eliminar-grupo', [DashboardController::class, 'eliminar']);
$router->post('/eliminar-grupo', [DashboardController::class, 'eliminar']);
$router->get('/grupo', [DashboardController::class, 'grupo']);
$router->get('/deudas', [DashboardController::class, 'deudas']);
$router->get('/analisis', [DashboardController::class, 'analisis']);
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);
$router->get('/cambiar-pass', [DashboardController::class, 'cambiarPass']);
$router->post('/cambiar-pass', [DashboardController::class, 'cambiarPass']);

// Tratamiento de Miembros del grupo
$router->get('/miembros', [MiembroController::class, 'index']);
$router->get('/alta-miembro', [MiembroController::class, 'crear']);
$router->post('/alta-miembro', [MiembroController::class, 'crear']);

// API de miembros
$router->get('/api/miembros', [APImiembros::class, 'index']);
$router->get('/api/miembros-activos', [APImiembros::class, 'miembrosActivos']);
$router->post('/api/miembro/crear', [APImiembros::class, 'crear']);
$router->post('/api/miembro/actualizar', [APImiembros::class, 'actualizar']);
$router->post('/api/miembro/actualizarSoyYo', [APImiembros::class, 'actualizarSoyYo']);
$router->post('/api/miembro/actualizarPeso', [APImiembros::class, 'actualizarPeso']);
$router->post('/api/miembro/actualizarActivo', [APImiembros::class, 'actualizarActivo']);
$router->post('/api/miembro/eliminar', [APImiembros::class, 'eliminar']);

// APIs para las tablas de parámetros
$router->get('/api/conceptos', [APIconceptos::class, 'index']);
$router->get('/api/tipos', [APItipos::class, 'index']);

// APIs para los movimientos
$router->get('/api/movimientos', [APImovimientos::class, 'index']);
$router->post('/api/movimiento/crear', [APImovimientos::class, 'crear']);
$router->post('/api/movimiento/actualizar', [APImovimientos::class, 'actualizar']);
$router->post('/api/movimiento/eliminar', [APImovimientos::class, 'eliminar']);
$router->get('/api/movimientos/gastos', [APImovimientos::class, 'gastos']);
$router->get('/api/movimientos/saldos', [APImovimientos::class, 'saldos']);
$router->get('/api/movimientos/deudas', [APImovimientos::class, 'deudas']);

// APIs para los saldos
$router->get('/api/saldos', [APIsaldos::class, 'index']);
$router->post('/api/saldo/crear', [APIsaldos::class, 'crear']);
$router->post('/api/saldo/actualizar', [APIsaldos::class, 'actualizar']);
$router->post('/api/saldo/eliminar', [APIsaldos::class, 'eliminar']);

// APIs para los grupos
$router->get('/api/grupos', [APIgrupos::class, 'index']);
$router->get('/api/grupo/getGrupo', [APIgrupos::class, 'getGrupo']);

// APIs para las deudas
$router->get('/api/deudas', [APIdeudas::class, 'index']);
$router->post('/api/deuda/crear', [APIdeudas::class, 'crear']);
$router->post('/api/deuda/actualizar', [APIdeudas::class, 'actualizar']);
$router->post('/api/deuda/eliminar', [APIdeudas::class, 'eliminar']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();