<?php namespace App\Storage;

class SessionStorage extends StorageManager
{
    private const BYTE_SIZE = 32;
    private static array $sessionData = [];

    public function __construct()
    {
        if(!$this->sessionExists()) {
            $this->start();
        }
    }
    
    public function start(): void
    {
        if(!$this->sessionExists()) {
            session_start();
            self::$sessionData = $_SESSION;
        }

        if(!$this->has('csrf_token')) {
            $this->store('csrf_token', bin2hex(random_bytes(self::BYTE_SIZE)));
        }
    }

    public function has(string $key): bool
    {
        return (isset(self::$sessionData[$key]) && !empty(self::$sessionData[$key]));
    }
    
    public function store(string $key, $value): void
    {
        self::$sessionData[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset(self::$sessionData[$key]);
    }

    public function retrieve(string $key): mixed
    {
        return self::$sessionData[$key] ?? null;
    }

    public function sessionExists(): bool
    {
        if(session_status() !== PHP_SESSION_ACTIVE && empty(self::$sessionData)) {
            return false;
        }
        else {
            return true;
        }
    }

    public function getCSRF(): string 
    {
        return self::$sessionData['csrf_token'];
    }
}