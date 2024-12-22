<?php
namespace App;

class Router {
    private $routes = [];

    public function get($path, $action) {
        $this->addRoute('GET', $path, $action);
    }

    public function post($path, $action) {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute($method, $path, $action) {
        $this->routes[] = compact('method', 'path', 'action');
    }

    public function dispatch($method, $path) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $action = $route['action'];
                if (is_callable($action)) {
                    call_user_func($action);
                    return;
                } elseif (is_string($action)) {
                    // Parse controller@method
                    list($controller, $methodName) = explode('@', $action);
                    $controller = "App\\Http\\Controllers\\$controller";
                    if (class_exists($controller)) {
                        $controllerInstance = new $controller();
                        if (method_exists($controllerInstance, $methodName)) {
                            call_user_func([$controllerInstance, $methodName]);
                            return;
                        }
                    }
                }
            }
        }

        // Jika route tidak ditemukan
        http_response_code(404);
        echo "404 Not Found";
    }
}
?>
