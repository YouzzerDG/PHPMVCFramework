<?php namespace App\Requests;

interface IRequest {

    /**
     * Checks if item exists with specified key in array of request type.
     * 
     * @return true if key is found.
     * @return false else return false.
     * */
    public function has(string $key): bool;

    /**
     * Checks first if value of key in array of type request exists.
     * 
     * @param string $key array key of item in type request array.
     * @param mixed $defaultFallback default value to fallback on if item does not exists.
     * 
     * @return mixed gets value of specified key if found, else return $defaultFallback.
     * */
    public function get(string $key, mixed $defaultFallback = null): mixed;

    /**
     * Sets new item in array of type request.
     **/ 
    public function set(string $key, mixed $value): void;

    /**
     * Returns array of request type.
     * 
     * @return array|null array of request type or null if not found.
     * */
    public function all(): array|null;

    /**
     * Removes item with specified key from array of request type.
     * */
    public function remove(string $key): void;
}