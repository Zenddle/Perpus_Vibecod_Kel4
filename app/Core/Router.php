<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    // $path boleh memakai parameter, mis. '/books/{id}'
    // $middleware: array nama middleware, mis. ['auth'] atau ['auth','admin']
    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        // Path rute hanya berisi huruf, angka, '/', '-' dan {param}, jadi aman tanpa preg_quote
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[$method][] = [$regex, $handler, $middleware];
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = '/' . trim(rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '/'), '/'); // decode: nama folder bisa berspasi

        // Buang folder proyek (mis. /MVC-polosan) dari URL
        if (BASE_PATH !== '' && str_starts_with($path, BASE_PATH)) {
            $path = '/' . trim(substr($path, strlen(BASE_PATH)), '/');
        }

        foreach ($this->routes[$method] ?? [] as [$regex, $handler, $middleware]) {
            if (!preg_match($regex, $path, $m)) continue;

            // Semua POST wajib membawa token CSRF yang valid
            if ($method === 'POST') {
                $token   = (string)($_POST['_token'] ?? '');
                $session = (string)($_SESSION['csrf'] ?? '');
                if ($session === '' || !hash_equals($session, $token)) {
                    http_response_code(419);
                    exit('419 - Sesi kedaluwarsa, muat ulang halaman lalu coba lagi.');
                }
            }

            foreach ($middleware as $name) {
                AuthMiddleware::handle($name);
            }

            $params = array_values(array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY));
            [$class, $action] = $handler;
            (new $class())->$action(...$params);
            return;
        }

        http_response_code(404);
        echo '404 - Halaman tidak ditemukan';
    }
}
