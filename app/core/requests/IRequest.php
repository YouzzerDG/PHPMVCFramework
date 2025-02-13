<?php namespace App\Requests;

interface IRequest {

    /**
     * Check if request has data.
     * 
     * @return true if request has data.
     * @return false if rquest has no data.
     * */
    public static function hasData(): bool;

    /**
     * Returns array of type request.
     * 
     * @param string $prefix specified string to filter on array keys.
     * @param bool $trimPrefix set to true to remove '$prefix' from array keys.
     * 
     * @return array returns array of type request.
     * @return null if data of type request is empty.
     * */
    public static function get(string $prefix = '', bool $trimPrefix = false): array|null;
}