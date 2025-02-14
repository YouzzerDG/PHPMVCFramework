<?php namespace App\Storage;

/**
 * Interface IStorage
 *
 * An interface that defines the basic functionality for a storage.
 * It can be implemented by various storage methods, such as sessions and cookies.
 * Each storage method must implement the standard methods for 
 * managing storage, retrieving, storing, and deleting data.
 *
 * @package App\Storage
 **/
interface IStorage
{
    /**
     * Initilizes the storage. For example, starting a session or setting initial cookies.
     *
     * @return void
     **/
    public function start(): void;

    /**
     * Check if a value exists in the storage with the provided key.
     *
     * @param string $key The key to check if it exists in the storage.
     * @return bool Returns true if the value exists, false otherwise.
     */
    public function has(string $key): bool;

    /**
     * Retrieve a value from storage with provided key.
     *
     * @param string $key The key used to retrieve the stored value.
     * @return mixed The stored value or null if the key does not exist.
     **/
    public function retrieve(string $key): mixed;

    /**
     * Stores a value in storage associated with the provided key.
     *
     * @param string $key The key under which the value will be stored.
     * @param mixed $value The value to be stored.
     * @return void
     **/
    public function store(string $key, $value): void;

    /**
     * Delete a stored value with the provided key.
     *
     * @param string $key The key to be removed from the storage.
     * @return void
     **/
    public function delete(string $key): void;
}