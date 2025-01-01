<?php namespace App;

abstract class View
{
    public static function render(string $view): string|false
    {
        $viewDir = APP_PATH . 'views/' . $view . '.php';

        ob_start();

        require_once $viewDir;

        return ob_get_clean();
    }
}