<?php namespace App;


use JetBrains\PhpStorm\NoReturn;

class Route
{
    private array $routes;

    public function register(array $routes): Route
    {
        //todo: lees batin dm routing met parameters
        foreach ($routes as $route) {
            $uri = $route[0];
            $controller = $route[1];
            $action = $route[2];

            $this->routes[$uri] = [
                'controller' => $controller,
                'action' => $action
            ];
        }

        print_r($this->routes);


        return $this;
    }

    #[NoReturn]
    public function run(): void
    {
        $currentPage = '/' . $_GET['_url'] ?? '/';//(!isset($_GET['_url']) || $_GET['_url'] == '' ? '' : $_GET['_url']);

        if (preg_match('(\W:[a-zA-Z]*)', $uri)) {

        }

        $route = $this->routes[$currentPage];

        (new $route['controller']())->{$route['action']}();

        exit;
    }
}