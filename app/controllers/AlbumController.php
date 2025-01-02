<?php namespace Controller;

use App\View;
use Model\Album;

class AlbumController implements IController
{
    public function index(): void
    {
        echo View::render('album/index');
    }

    public function show($id): void
    {
        echo $id;

        // TODO: statically call Modelname::get() with id from uri
        //$album = Album::get($id);
        echo View::render('album/detail');
    }

    public function create(): void
    {
        echo View::render('album/create');
    }

    public function update($id): void
    {
        echo "Updating $id...";
    }

    public function delete($id): void
    {
        echo "Deleting $id...";
    }
}