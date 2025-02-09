<?php

namespace Controller;

use App\View;
use Controller\IController;

class ServiceController implements IController
{

    public function index(): void
    {
        echo View::render("services/index");
    }

    public function detail($id): void
    {

    }

    public function create(): void
    {

    }

    public function edit($id): void
    {
        
    }

    public function update($id): void
    {

    }

    public function delete($id): void
    {

    }
}