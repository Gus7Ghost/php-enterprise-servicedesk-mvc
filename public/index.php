<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
\App\Core\Autoloader::register();

use App\Core\Router;
use App\Core\Session;

Session::start();

$router = new Router();

// Rutas de Autenticación
$router->get('/', 'DashboardController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Rutas de Dashboard y Reportes
$router->get('/dashboard', 'DashboardController@index');

// Rutas de Incidentes / Tickets
$router->get('/tickets', 'TicketController@index');
$router->get('/tickets/create', 'TicketController@create');
$router->post('/tickets', 'TicketController@store');
$router->get('/tickets/{id}', 'TicketController@show');
$router->post('/tickets/{id}/comments', 'TicketController@addComment');
$router->post('/tickets/{id}/status', 'TicketController@updateStatus');
$router->post('/tickets/{id}/assign', 'TicketController@assignAgent');

// Despachar la petición HTTP actual
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
