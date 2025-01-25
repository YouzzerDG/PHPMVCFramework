<?php
//Require needed files

require_once 'server.php';
require_once 'app/config/config.php';
require_once 'vendor/autoload.php';

(new \App\Route())->register([
    ['/', Controller\HomeController::class, 'index'],
    ['/reservations', Controller\ReservationController::class, 'index'],
    ['/reservations/add', Controller\ReservationController::class, 'create'],
    ['/reservations/:id', Controller\ReservationController::class, 'show'],
    ['/reservations/:id/update', Controller\ReservationController::class, 'update'],
    ['/reservations/:id/delete', Controller\ReservationController::class, 'delete'],

    // ['/albums', Controller\AlbumController::class, 'index'],
    // ['/albums/add', Controller\AlbumController::class, 'add'],
    // ['/albums/:id', Controller\AlbumController::class, 'show'],
    // ['/albums/:id/update', Controller\AlbumController::class, 'update'],
    // ['/albums/:id/delete', Controller\AlbumController::class, 'delete'],
]);
