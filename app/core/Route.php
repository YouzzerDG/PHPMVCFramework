<?php namespace App;

class Route
{
    private array $routes;

    public function register(array $routes): void
    {
        foreach ($routes as $route) {
            $uri = $route[0];
            $segments = explode('/', ltrim($uri, '/'));

            $controller = $route[1];
            $action = $route[2];

            $uriIdentifier = $this->createUriIdentifier($segments);

            $this->routes[$uriIdentifier] = [
                'controller' => $controller,
                'action' => $action,
                'segments' => $uri === '/' || $uri === '' ? [] : $segments,
            ];

            if (str_contains($uri, ':')) {
                $this->routes[$uriIdentifier]['parameters'] = $this->setParameters($segments);
            }
        }

        $this->run();
    }

    private function createUriIdentifier(array $uriSegments): string
    {
        if (empty($uriSegments)) return '/';

        foreach ($uriSegments as &$segment) {
            if (str_starts_with($segment, ':')) {
                $segment = ':';
            }
        }

        return '/' . implode('/', $uriSegments);
    }

    private function setParameters(array $uriSegments): array
    {
        $params = [];

        foreach ($uriSegments as $key => $segment) {
            if (str_starts_with($segment, ':')) {
                $params[$key] = $segment;
            }
        }

        return $params;
    }

    private function run(): void
    {
        $currentPage = '/' . $_GET['_url'] ?? '/';

        // Load controller if route is found
        if (isset($this->routes[$currentPage])) {
            (new $this->routes[$currentPage]['controller']())->{$this->routes[$currentPage]['action']}();
        } else {
            $currentPageSegments = explode('/', ltrim($currentPage, '/'));

            $args = [];
            foreach ($currentPageSegments as $key => &$uriSegment) {
                foreach (array_keys($this->routes) as $routeKey) {
                    // Skip home & routes with no parameters.
                    if ($routeKey === '/' || !str_contains($routeKey, ':')) continue;

                    // If segment of uri is not found in the route key
                    // AND if segment is bound to a parameter,
                    // get the parameter name, and it binds to the value of the uri segment.
                    if (!strpos($routeKey, $uriSegment) && isset($this->routes[$routeKey]['parameters'][$key])) {
                        $param = ltrim($this->routes[$routeKey]['parameters'][$key], ':');

                        $args[$param] = $uriSegment;
                        $uriSegment = ':';
                    }
                }
            }

            $route = '/' . implode('/', $currentPageSegments);

            if(isset($this->routes[$route])) {
                $controller = new $this->routes[$route]['controller']();

                if (method_exists($controller, $this->routes[$route]['action'])) {
                    call_user_func_array([$controller, $this->routes[$route]['action']], $args);
                }
            }
            else {
                echo 404;
            }
        }

        exit;
    }
}