<?php namespace App;

abstract class Utils
{
    /**
     * Returns given array while removing substring from array keys.
     * Or returns null if either parameters are empty.
     **/
    public static function subTrimArrayKeys(string $subString, array $data): array|null
    {
        if(empty($subString) ||empty($data)){
            return null;
        }
        
        return array_combine(
            array_map(
                function ($key) use ($subString) {
                    return str_replace($subString, '', $key);
                }, 
                array_keys($data)
            ), 
        $data);
    }
}