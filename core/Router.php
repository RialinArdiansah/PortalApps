<?php
class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middleware'  => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = $this->pathToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    if (is_array($mw)) {
                        call_user_func($mw);
                    } elseif (is_string($mw) && str_contains($mw, '::')) {
                        call_user_func(explode('::', $mw));
                    } else {
                        call_user_func($mw);
                    }
                }

                // Resolve controller
                [$controllerClass, $action] = $route['handler'];
                $controllerFile = BASE_PATH . "/controllers/{$controllerClass}.php";
                if (!file_exists($controllerFile)) {
                    http_response_code(500);
                    echo "Controller not found: {$controllerClass}";
                    return;
                }
                require_once $controllerFile;
                $controller = new $controllerClass();

                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        require_once BASE_PATH . '/views/errors/404.php';
    }

    private function pathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return "#^{$pattern}$#";
    }
}
