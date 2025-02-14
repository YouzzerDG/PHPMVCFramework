<?php namespace App\Storage;

abstract class StorageManager implements IStorage
{
    public function start(): void {}
    
    public function store(string $key, $value): void
    {

    }

    public function delete(string $key): void
    {

    }

    public function retrieve(string $key): mixed
    {
        return [];
    }
}