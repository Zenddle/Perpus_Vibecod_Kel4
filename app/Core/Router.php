<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    // $middleware: array nama middleware, mis. ['auth'] atau ['auth','admin']
    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->routes['GET'][$path] = [$handler, $middleware];
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->routes['POST'][$path] = [$handler, $middleware];
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

<<<<<<< HEAD
        if (isset(self::$routes[$method][$url])) {
            $target = self::$routes[$method][$url];
            $controllerName = $target[0];
            $action = $target[1];

            // Path diperbaiki: naik satu folder ke app/, lalu masuk ke Controllers/
            require_once __DIR__ . '/../Controllers/' . $controllerName . '.php';
            
            $controller = new $controllerName();
            $controller->$action();
        } else {
            http_response_code(404);
            echo "404 - Halaman Tidak Ditemukan";
=======
        // Buang folder proyek (mis. /MVC-polosan) dari URL
        if (BASE_PATH !== '' && str_starts_with($path, BASE_PATH)) {
            $path = '/' . trim(substr($path, strlen(BASE_PATH)), '/');
>>>>>>> d80d0347eedc134252bcd32e4dd4b3210c948e17
        }

        $route = $this->routes[$method][$path] ?? null;
        if (!$route) {
            http_response_code(404);
            echo '404 - Halaman tidak ditemukan';
            return;
        }

        [$handler, $middleware] = $route;
        foreach ($middleware as $name) {
            AuthMiddleware::handle($name);
        }

        [$class, $action] = $handler;
        (new $class())->$action();
    }
}
