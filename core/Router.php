<?php
// core/Router.php

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Detectar el subdirectorio base automáticamente
        // Ejemplo: /betime_sena/public/login/aprendiz → /login/aprendiz
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = '/' . ltrim($uri, '/');

        // Quitar trailing slash excepto en raíz
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        if (isset($this->routes[$method][$uri])) {
            [$controllerClass, $methodName] = $this->routes[$method][$uri];
            require_once __DIR__ . '/../app/controllers/' . $controllerClass . '.php';
            $controller = new $controllerClass();
            $controller->$methodName();
            return;
        }

        // 404
        http_response_code(404);
        require_once __DIR__ . '/../app/views/404.php';
    }
}
