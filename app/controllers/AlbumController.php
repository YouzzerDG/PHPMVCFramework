<?php namespace Controller;

use App\View;

class AlbumController
{
    public function index(): void
    {
        echo View::render('album/index');
    }

    public function show($id)
    {
        echo $id;
        echo View::render('album/detail');
    }
}