<?php

class Route {
    private static $routes = [];

    public static function get($url, $target) {
        self::$routes['GET'][trim($url, '/')] = $target;
    }

    public static function post($url, $target) {
        self::$routes['POST'][trim($url, '/')] = $target;
    }

    public static function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

        if (isset(self::$routes[$method][$url])) {
            $target = self::$routes[$method][$url];
            $controllerName = $target[0];
            $action = $target[1];

            require_once __DIR__ . 'app/controllers/' . $controllerName . '.php';
            $controller = new $controllerName();
            $controller->$action();
        } else {
            http_response_code(404);
            echo "404 - Halaman Tidak Ditemukan";
        }
    }
}