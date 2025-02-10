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

    public static function getData(string $prefix = ''): array|null
    {                
        if(self::hasData() && isset($_POST['hmn']) && $_POST['hmn'] == true) {
            if($prefix !== '') {
                return array_filter($_POST, function ($key) use ($prefix) {
                    return str_contains($key, $prefix);
                }, ARRAY_FILTER_USE_KEY);
            } else {
                return array_slice($_POST, 1, count($_POST), true);
            }
        }

        return null;
    }
}