<?php

namespace Controller;

use App\View;

class HomeController implements IController
{
    public function index(): void
    {
        echo View::render('home/index');
    }

    public function show($id): void {}
    public function create(): void {}
    public function update($id): void {}
    public function delete($id): void {}
}
