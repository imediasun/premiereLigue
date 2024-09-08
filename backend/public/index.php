<?php
// Entry point of the application.
// Including the autoloader and initializing the Router.

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router;

// Create a router instance and run it.
$router = new Router();
$router->run();
