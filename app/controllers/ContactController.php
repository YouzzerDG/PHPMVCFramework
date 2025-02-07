<?php

namespace Controller;

use App\View;
use Model\Contact;

class ContactController implements IController
{
    public function index(): void
    {
        $contacts = Contact::all();

        var_dump($contacts);
        
        echo View::render('contacts/index');
    }

    public function show($id): void 
    {
        $contact = Contact::find(['id' => $id]);

        var_dump($contact);
    }
    public function create(): void {}
    public function update($id): void {}
    public function delete($id): void {}
}
