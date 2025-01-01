<?php
//Require needed files

require_once 'server.php';
require_once 'app/config/config.php';
require_once 'vendor/autoload.php';

(new \App\Route())->register([
    ['/', Controller\HomeController::class, 'index'],
    ['/albums', Controller\AlbumController::class, 'index'],
    ['/album/:id', Controller\AlbumController::class, 'show'],
])->run();
