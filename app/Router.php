<?php

namespace App;

class Router
{
    protected $routesGet = [];
    protected $routesPost = [];

    public function get($path, $action)
    {
        $this->routesGet[$path] = $action;
    }

    public function post($path, $action)
    {
        $this->routesPost[$path] = $action;
    }

    public function dispatch($method, $uri)
    {
        // cek method (GET/POST)
        if ($method === 'GET') {
            if (isset($this->routesGet[$uri])) {
                return $this->callAction($this->routesGet[$uri]);
            }
        } elseif ($method === 'POST') {
            if (isset($this->routesPost[$uri])) {
                return $this->callAction($this->routesPost[$uri]);
            }
        }

        // 404
        echo "404 Not Found - Path: $uri";
    }

    protected function callAction($action)
    {
        // $action bisa 'AuthController@showLogin'
        if (is_callable($action)) {
            // closure anonymous function
            return call_user_func($action);
        } elseif (is_string($action)) {
            // format "Controller@method"
            if (strpos($action, '@') !== false) {
                list($controller, $method) = explode('@', $action);
                $controller = "\\App\\Http\\Controllers\\$controller";
                $obj = new $controller();
                return $obj->$method();
            }
        }

        echo "Invalid route action";
    }
}
