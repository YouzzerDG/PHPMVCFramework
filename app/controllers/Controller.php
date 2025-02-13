<?php namespace Controller;

use App\View;
use App\Requests;

abstract class Controller
{
    public function __construct()
    {
        $postRequest = new Requests\PostRequest;

        if($postRequest->has('doPost') && $postRequest->has('hmn') && $postRequest->get('hmn') == true) {
            $postRequest->remove('doPost');
            $postRequest->remove('hmn');

            foreach($postRequest->all() as $key => $post) {
                $postRequest->set($key, $this->sanitize($post));
            }
        }
    }
    
    /**
     * Renders given view with using /views/template.php as base.
     **/ 
    public function render(View $view): void
    {
        echo (new View('template', ['renderData' => $view]));
    }

    /**
     * Renders view with entirely own HTML within.
     **/ 
    public function renderStandalone(View $view): void
    {
        echo $view;
    }

    private function sanitize(string $dirtyString): string
    {
        return htmlspecialchars(strip_tags($dirtyString), ENT_QUOTES, 'UTF-8');
    }
}