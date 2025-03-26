<?php

class Router
{
    private $routes = [];

    public function addRoute(string $path, string $controller, string $action): void
    {
        $this->routes[$path] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch(string $path): void
    {
        if (array_key_exists($path, $this->routes)) {
            $controllerName = $this->routes[$path]['controller'];
            $actionName = $this->routes[$path]['action'];

            $controllerClass = "App\\Controller\\$controllerName";
            require_once __DIR__ . "/Controller/$controllerName.php";

            $controller = new $controllerClass();
            $controller->$actionName();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found";
        }
    }
}