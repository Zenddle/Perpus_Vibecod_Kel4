<?php

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/app/Core/Router.php';

use App\Core\Router;

$router = new Router();

require_once BASE_PATH . '/app/Routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);