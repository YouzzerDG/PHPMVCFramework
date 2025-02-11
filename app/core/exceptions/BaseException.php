<?php namespace App\Exceptions;


abstract class BaseException extends \Exception 
{
    public function __construct(BaseException $e) {
        parent::__construct($e->getMessage(), $e->getCode(), $e->getPrevious());
        (new \App\View('error', ['exception' => $e]))->renderStandalone();
        exit;
    }
}