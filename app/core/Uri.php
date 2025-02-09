<?php namespace App;

abstract class Uri {

    public static function __callStatic($name, $arguments)
    {
        // var_dump('call statuc');
        // var_dump($name, $arguments);
        return $name;
    }

    public static function get(): string
    {
        return isset($_SERVER['HTTPS']) ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'] . strtok($_SERVER['REQUEST_URI'],'?'); 
    }
}