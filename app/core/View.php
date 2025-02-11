<?php namespace App;

class View
{
    private string $viewData;
    
    public function __construct(
        private string $view,
        private array $viewTemplateData = []
    ) {
        $viewFile = APP_PATH . 'views/' . $view . '.php';

        if(!file_exists($viewFile)) {
            require APP_PATH . 'views/404.php';
            exit;
        }

        $this->viewData = $this->inject($viewFile, $viewTemplateData);
    }
    
    public function renderStandalone(): void
    {
        echo $this->viewData;
    }

    public function render(): void
    {
        echo $this->inject(APP_PATH . 'views/template.php', ['renderData' => $this->viewData]);
    }

    private function inject(string $file, array $data = []): string|false
    {
        ob_start();
        
        extract($data, EXTR_SKIP);
        require $file;

        return ob_get_clean();
    }
}