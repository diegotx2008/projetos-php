<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Controllers\Auth\AuthController;
use App\Controllers\HomeController;
use App\Controllers\Filmes\FilmeController;
use App\Controllers\Series\SerieController;
use App\Controllers\Animes\AnimeController;
use App\Controllers\Admin\GeneroController;
use App\Controllers\Admin\ListaController;

session_start();

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$router = new Router();

// Rotas Públicas
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Rotas Protegidas
$router->get('/', [HomeController::class, 'index'], true);

// Filmes
$router->get('/filmes', [FilmeController::class, 'index'], true);
$router->post('/filmes/salvar', [FilmeController::class, 'salvarNaLista'], true);

// Séries
$router->get('/series', [SerieController::class, 'index'], true);

// Animes
$router->get('/animes', [AnimeController::class, 'index'], true);

// Administrativo / Cadastros
$router->get('/admin/generos', [GeneroController::class, 'index'], true);
$router->post('/admin/generos', [GeneroController::class, 'store'], true);
$router->get('/admin/listas', [ListaController::class, 'index'], true);
$router->post('/admin/listas', [ListaController::class, 'store'], true);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);