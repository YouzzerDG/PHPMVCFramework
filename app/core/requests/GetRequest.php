<?php namespace App\Requests;

class GetRequest extends Request {
    public function __construct()
    {
        return parent::__construct('_get');
    }
}