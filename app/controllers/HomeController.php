<?php namespace Controller;

use App\View;

class HomeController
{
    public function index(): void
    {
        echo View::render('home/index', [
            'pageInfo' => [
                'pageTitle' => 'Home',
                'pageDescription' => 'home pagina!'
            ]
        ]);
    }

    public function show($id): void {}
    public function create(): void {}
    public function update($id): void {}
    public function delete($id): void {}
}
