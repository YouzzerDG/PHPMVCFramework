<?php

namespace Controller;

use App\View;
use Model\Contact;
use App\Requests\PostRequest;
use App\Route;

class ContactController implements IController
{
    public function index(): void
    {
        $contacts = Contact::all();
        
        var_dump(new View('contacts/index'));

        (new View('contacts/index'))->render();
    }

    public function detail($id): void 
    {
        $contact = Contact::find(['id' => $id]);

        (new View('contacts/detail', ['contact' => $contact]))->render();
    }

    public function create(): void 
    {
        if(PostRequest::hasData()) {
            $postData = PostRequest::get('contact_', true);

            $msg = 'Mislukt!';
            if(Contact::add($postData)) {
                $msg = 'Contact succesvol opgeslagen!';
                Route::DirectTo('/contacts');
            }
        }
        else {
            (new View('contacts/create'))->render();
        }
    }
    
    public function edit($id): void
    {
        $contact = Contact::find(['id' => $id]);

        (new View('contacts/edit', ['contact' => $contact]))->render();
    }

    public function update($id): void {}
    
    public function delete($id): void {}
}
