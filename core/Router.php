<?php

namespace Core;

class Router
{
    private array $routes = [];

    /*
    |--------------------------------------------------------------------------
    | Enregistrement des routes
    |--------------------------------------------------------------------------
    */
    public function get(string $path, array|callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array|callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, array|callable $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, array|callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, array|callable $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'handler' => $handler,
            'pattern' => $this->buildPattern($path),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dispatch
    |--------------------------------------------------------------------------
    */
    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                $request->setParams($params);

                $response = $this->callHandler($route['handler'], $request);
                $response->send();
                return;
            }
        }

        // Aucune route trouvée
        Response::notFound('Route non trouvée')->send();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    private function buildPattern(string $path): string
    {
        // Convertit /users/:id en regex nommée
        $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function callHandler(array|callable $handler, Request $request): Response
    {
        if (is_callable($handler)) {
            return $handler($request);
        }

        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            return Response::error("Contrôleur {$controllerClass} introuvable", 500);
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            return Response::error("Méthode {$method} introuvable", 500);
        }

        return $controller->$method($request);
    }
}
