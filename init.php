<?php
//Require needed files

require_once 'server.php';
require_once 'app/config/config.php';
require_once 'vendor/autoload.php';

(new \App\Route())->register([
    ['/', Controller\HomeController::class, 'index'],
    ['/albums', Controller\AlbumController::class, 'index'],
    ['/albums/add', Controller\AlbumController::class, 'add'],
    ['/albums/:id', Controller\AlbumController::class, 'show'],
    ['/albums/:id/update', Controller\AlbumController::class, 'update'],
    ['/albums/:id/delete', Controller\AlbumController::class, 'delete'],
]);
