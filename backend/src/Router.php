<?php

class Router {
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function get($route, $controllerAction) {
        $this->routes['GET'][$route] = $controllerAction;
    }

    public function post($route, $controllerAction) {
        $this->routes['POST'][$route] = $controllerAction;
    }

    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $this->cleanUri($_SERVER['REQUEST_URI']);

        $requestUri = explode('?', $requestUri)[0];

        if (isset($this->routes[$requestMethod][$requestUri])) {
            list($controller, $action) = explode('@', $this->routes[$requestMethod][$requestUri]);
            if (class_exists($controller) && method_exists($controller, $action)) {
                $controllerInstance = new $controller();
                $controllerInstance->$action();
            } else {
                http_response_code(500);
                echo "Controller or method not found.";
            }
        } else {
            http_response_code(404);
            echo "Page not found.";
        }
    }

    private function cleanUri($uri) {
        return rtrim($uri, '/'); // Удаляем завершающие слеши
    }
}
