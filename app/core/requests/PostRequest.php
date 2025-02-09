<?php namespace App\Requests;


class PostRequest {

    public static function __callStatic($name, $arguments)
    {
        var_dump('call statuc');
        var_dump($name, $arguments);
    }
    
    public static function hasData(): bool
    {
        if(!empty($_POST)) {
            return true;
        }

        return false;
    }

    public static function getData(): array|null
    {
        if(self::hasData()) {
            return $_POST;
        }

        return null;
    }
}