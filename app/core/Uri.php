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
        return self::getBaseUrl() . strtok($_SERVER['REQUEST_URI'],'?'); 
    }

    public static function getBaseUrl(): string
    {
        return isset($_SERVER['HTTPS']) ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'];
    }
}