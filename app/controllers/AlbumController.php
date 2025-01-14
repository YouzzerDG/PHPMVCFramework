<?php namespace Controller;

use App\View;
use Model\Album;

class AlbumController implements IController
{
    public function index(): void
    {

        //$albums = Albums::all();

        echo View::render('album/index');
    }

    public function show($id): void
    {
        // echo $id;
        // var_dump(__FUNCTION__);

        // TODO: statically call Modelname::get() with id from uri
        $album = Album::where($id);


        // print_r($album);
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