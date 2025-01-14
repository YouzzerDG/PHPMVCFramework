<?php

namespace Model;

use App\Database;

#[\AllowDynamicProperties]
abstract class Model
{
    private \PDO $db;
    protected static array $table = [
        'name' => '',
        'columns' => []
    ];
    protected static array $constraints = [];


    public function __construct()
    {

    }

    public static function __callStatic($name, $arguments)
    {
        var_dump($name, $arguments);
    }

    public static function where(mixed $params) 
    {

    }
}