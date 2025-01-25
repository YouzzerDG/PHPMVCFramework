<?php namespace App;

abstract class View
{
    public static function render(string $view, array $templateData = []): string|false
    {
        $viewDir = APP_PATH . 'views/' . $view . '.php';

        if (!empty($templateData))
            extract($templateData);

        ob_start();

        require_once $viewDir;

        return ob_get_clean();
    }
}