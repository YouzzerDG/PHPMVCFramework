<?php

namespace Controller;

use App\View;
use Model\Contact;
use App\Requests\PostRequest;

class ContactController implements IController
{
    public function index(): void
    {
        $contacts = Contact::all();

        if(PostRequest::hasData()) {
            var_dump(PostRequest::getData());
        }

        // var_dump($contacts);
        
        echo View::render('contacts/index');
    }

    public function detail($id): void 
    {
        $contact = Contact::find(['id' => $id]);

        // var_dump($contact);

        echo View::render('contacts/detail', ['contact' => $contact]);
    }

    public function create(): void 
    {
        if(PostRequest::hasData()) {
            $postData = PostRequest::getData();

            Contact::add($postData);
        }
        else {
            echo View::render('contacts/create');
        }
    }
    
    public function edit($id): void
    {
        $contact = Contact::find(['id' => $id]);

        // var_dump($contact);

        echo View::render('contacts/edit', ['contact' => $contact]);
    }

    public function update($id): void {}
    
    public function delete($id): void {}
}
