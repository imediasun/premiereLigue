<?php
// This class handles the routing logic for the API.
// Routes requests to appropriate controllers based on the URI.

namespace App;

use App\Controller\LeagueController;

class Router {
    private $getRoutes = [
        '/api/table' => [LeagueController::class, 'getTable'],
        '/api/play' => [LeagueController::class, 'playNextRound'],
    ];

    private $postRoutes = [
        '/api/reset' => [LeagueController::class, 'resetLeague'],
    ];

    public function run() {
        // Determine the request method and URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Route GET requests
        if ($method === 'GET' && isset($this->getRoutes[$uri])) {
            $controllerAction = $this->getRoutes[$uri];
            [$controller, $action] = $controllerAction;
            (new $controller())->$action();
            return;
        }

        // Route POST requests
        if ($method === 'POST' && isset($this->postRoutes[$uri])) {
            $controllerAction = $this->postRoutes[$uri];
            [$controller, $action] = $controllerAction;
            (new $controller())->$action();
            return;
        }

        // If no route is found, return 404
        http_response_code(404);
        echo "Page not found.";
    }
}
