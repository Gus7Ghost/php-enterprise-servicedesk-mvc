<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        // Convertir parámetros {id} a expresiones regulares (?P<id>[a-zA-Z0-9_\-]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_\-]+)', $path);
        $pattern = "#^" . $pattern . "$#";

        $this->routes[$method][$pattern] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $parsedUri = parse_url($uri, PHP_URL_PATH);
        $cleanUri = rtrim($parsedUri, '/') ?: '/';

        $method = strtoupper($method);

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $handler) {
                if (preg_match($pattern, $cleanUri, $matches)) {
                    // Extraer solo los parámetros nombrados
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    [$controllerName, $action] = explode('@', $handler);
                    $fullController = "App\\Controllers\\{$controllerName}";

                    if (class_exists($fullController)) {
                        $controller = new $fullController();
                        if (method_exists($controller, $action)) {
                            call_user_func_array([$controller, $action], $params);
                            return;
                        }
                    }
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "<h1 style='text-align:center; font-family:sans-serif; margin-top:50px;'>404 - Página no encontrada</h1>";
    }
}
