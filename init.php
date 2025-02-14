<?php
//Require needed files

require_once 'server.php';
require_once 'app/config/config.php';
require_once 'vendor/autoload.php';

(new \App\Route())->register([
    ['/', Controller\HomeController::class, 'index'],
    ['/contacts', Controller\ContactController::class, 'index'],
    ['/contacts/:id', Controller\ContactController::class, 'detail'],
    ['/contacts/:id/delete', Controller\ContactController::class, 'delete'],
    ['/contacts/create', Controller\ContactController::class, 'create'],
    ['/reservations', Controller\ReservationController::class, 'index'],
    ['/reservations/create', Controller\ReservationController::class, 'create'],
    ['/reservations/:id', Controller\ReservationController::class, 'detail'],
    ['/reservations/:id/update', Controller\ReservationController::class, 'update'],
    ['/reservations/:id/delete', Controller\ReservationController::class, 'delete'],
]);
