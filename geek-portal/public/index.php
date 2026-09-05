<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GeekPortal\Core\Router;

$router = new Router();
$router->get('/', 'HomeController@index');
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);