<?php namespace App\Requests;

class PostRequest extends Request {
    public function __construct()
    {
        return parent::__construct('_post');
    }
}