<?php

namespace Controller;

use App\View;
use Model\Contact;
use App\Requests;
use App\Route;

class ContactController extends Controller implements IController
{
    public function index(): void
    {
        $this->render(new View('contacts/index'));
    }

    public function detail($id): void 
    {
        $contact = Contact::find(['id' => $id]);

        $this->render((new View('contacts/detail', ['contact' => $contact])));
    }

    public function create(): void 
    {
        $postRequest = new Requests\PostRequest();
        
        if(!empty($postRequest->all())) {
            $postData = $postRequest->getDataSet('contact_', true);

            $msg = 'Mislukt!';
            if(Contact::add($postData)) {
                $msg = 'Contact succesvol opgeslagen!';
                (new Route)->directTo('/contacts');
            }
        }
        else {
            $this->render((new View('contacts/create')));
        }
    }
    
    public function edit($id): void
    {
        $contact = Contact::find(['id' => $id]);

        $this->render((new View('contacts/edit', ['contact' => $contact])));
    }

    public function update($id): void {}
    
    public function delete($id): void 
    {
        
    }
}
