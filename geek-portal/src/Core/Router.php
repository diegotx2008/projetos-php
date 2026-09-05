<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, bool $protected = false): void
    {
        $this->addRoute('GET', $path, $handler, $protected);
    }

    public function post(string $path, array $handler, bool $protected = false): void
    {
        $this->addRoute('POST', $path, $handler, $protected);
    }

    private function addRoute(string $method, string $path, array $handler, bool $protected): void
    {
        $this->routes[] = [
            'method'    => $method,
            'path'      => $path,
            'controller'=> $handler[0],
            'action'    => $handler[1],
            'protected' => $protected
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                if ($route['protected']) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    if (!isset($_SESSION['user_id'])) {
                        header('Location: /login');
                        exit;
                    }
                }

                $controller = new $route['controller']();
                $action = $route['action'];
                $controller->$action();
                return;
            }
        }

        http_response_code(404);
        echo "404 - Página não encontrada.";
    }
}