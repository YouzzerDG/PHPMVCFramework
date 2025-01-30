<?php namespace App;

abstract class View
{
    public static function render(string $view, array $viewTemplateData = []): string|false
    {
        $viewFile = APP_PATH . 'views/' . $view . '.php';
        $templateFile = APP_PATH . 'views/template.php';

        if(!file_exists($viewFile) || !file_exists($templateFile))
            return false;

        $renderData = self::inject($viewFile, $viewTemplateData);
        
        return self::inject($templateFile, ['renderData' => $renderData] + $viewTemplateData);
    }

    private static function inject(string $file, array $data = []): string|false
    {
        ob_start();
        
        extract($data, EXTR_SKIP);
        require $file;

        return ob_get_clean();
    }
}