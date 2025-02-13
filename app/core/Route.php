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

    public static function DirectTo(string $url): void
    {
        header("Location: " . \App\Uri::getBaseUrl() . $url);
        exit;
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

        //NOTE: Route *WILL* replace segments to placeholders *IF* one of the six base controller methods is not defined
        //      in routing registry (see init.php). E.g. /contacts/create *WILL* load as if it was meant for 'detail'
        //      method in the controller *IF* create is not defined.
        //TODO: Find a way if the segment of uri resembles one of the six base controller method names to not overwrite segment to placeholder
        //      and instead show 404 page.

        // Load controller if route is found
        if (isset($this->routes[$currentPage])) {
            (new $this->routes[$currentPage]['controller']())->{$this->routes[$currentPage]['action']}();
        } else {
            $currentPageSegments = explode('/', ltrim($currentPage, '/'));
            $getRequest = new \App\Requests\GetRequest();

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

                        $getRequest->set($param, $uriSegment);
                        $args[$param] = $uriSegment;
                        $uriSegment = ':';
                    }
                }
            }

            // var_dump($getRequest);

            $route = '/' . implode('/', $currentPageSegments);

            if(isset($this->routes[$route])) {
                $controller = new $this->routes[$route]['controller']();

                if (method_exists($controller, $this->routes[$route]['action'])) {
                    call_user_func_array([$controller, $this->routes[$route]['action']], $args);
                }
                else {
                    echo 'Functie bestaat niet!';
                }
            }
            else {
                echo 404;
            }
        }

        exit;
    }
}