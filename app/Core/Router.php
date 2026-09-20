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
        $path = '/' . trim(rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '/'), '/'); // decode: nama folder bisa berspasi

        // Buang folder proyek (mis. /MVC-polosan) dari URL
        if (BASE_PATH !== '' && str_starts_with($path, BASE_PATH)) {
            $path = '/' . trim(substr($path, strlen(BASE_PATH)), '/');
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
