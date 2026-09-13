<?php
// front controller que inicia a sessao, carrega o autoload e processa as rotas

//========================================================
//= Ponto de entrada unico da aplicacao
//========================================================

require_once dirname(__DIR__) . '/config/config.php';
require_once APP_PATH . '/Core/Autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ServiceController;

// Inicia a sessao do sistema
Session::start();

// Instancia o roteador de requisicoes
$router = new Router();
// Rotas de autenticacao e controle de acesso
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
// Rotas da tela principal do sistema
$router->get('/dashboard', [DashboardController::class, 'index']);
// Rotas para manutencao de servicos
$router->get('/services/create', [ServiceController::class, 'create']);
$router->post('/services/create', [ServiceController::class, 'store']);
$router->get('/services/edit', [ServiceController::class, 'edit']);
$router->post('/services/edit', [ServiceController::class, 'update']);
$router->post('/services/delete', [ServiceController::class, 'delete']);
$router->post('/services/finish', [ServiceController::class, 'finish']);
// Processa a rota atual
$router->dispatch();
