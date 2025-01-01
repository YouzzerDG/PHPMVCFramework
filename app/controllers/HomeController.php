<?php namespace Controller;

use App\View;

class HomeController
{
    public function index(): void
    {
        echo View::render('home/index');
    }
}