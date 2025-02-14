<?php namespace App\Storage;

class CookieStorage extends StorageManager
{
    public function store(string $key, $value): void
    {
        setcookie($key, $value, time() + 3600, "/");
    }

    public function has(string $key): bool
    {
        return (isset($_COOKIE[$key]) && !empty($_COOKIE[$key]));
    }

    public function delete(string $key): void
    {
        setcookie($key, '', time() - 3600, "/");
    }

    public function retrieve(string $key): mixed
    {
        return $_COOKIE[$key] ?? null;;
    }
}