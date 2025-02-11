<?php namespace App;

abstract class Utils
{
    public static function subTrimArrayKeys(string $subString, array $data): array|null
    {
        if(empty($data)){
            return null;
        }
        
        return array_combine(
            array_map(
                function ($key) use ($subString) {
                    return str_replace($subString . '_', '', $key);
                }, 
                array_keys($data)
            ), 
        $data);
    }
}