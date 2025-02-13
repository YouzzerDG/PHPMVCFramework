<?php namespace App\Requests;

abstract class Request implements IRequest
{
    public const GET 
    
    public static function hasData(): bool
    {
        if(!empty($_POST)) {
            return true;
        }

        return false;
    }

    public static function get(string $prefix = '', bool $trimPrefix = false): array|null
    {
        if(self::hasData() && isset($_POST['hmn']) && $_POST['hmn'] == true) {
            if($prefix !== '') {
                $filteredData = array_filter($_POST, function ($key) use ($prefix) {
                    return str_contains($key, $prefix);
                }, ARRAY_FILTER_USE_KEY);

                if($trimPrefix)
                    return \App\Utils::subTrimArrayKeys($prefix, $filteredData);
                else{
                    return $filteredData;
                }
            } else {
                return array_slice($_POST, 1, count($_POST), true);
            }
        }

        return null;
    }
}