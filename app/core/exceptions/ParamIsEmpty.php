<?php namespace App\Exceptions;


class ParamIsEmpty extends BaseException 
{
    public function __construct() {
        $this->message = "Param(s) of method {$this->getTrace()[0]['class']}{$this->getTrace()[0]['type']}{$this->getTrace()[0]['function']}() is empty!";
        $this->code = 19;
        parent::__construct($this);
    }
}