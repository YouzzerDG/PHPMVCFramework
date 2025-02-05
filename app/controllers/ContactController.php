<?php

namespace Controller;

use App\View;
use Model\Contact;

class ContactController implements IController
{
    public function index(): void
    {
        $contacts = Contact::all();
        
        echo View::render('contacts/index');
    }

    public function show($id): void {}
    public function create(): void {}
    public function update($id): void {}
    public function delete($id): void {}
}
