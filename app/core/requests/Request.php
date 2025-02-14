<?php namespace App\Requests;

abstract class Request implements IRequest
{
    /**
     * The global request data array.
     **/ 
    protected static array $requestData = [
        '_request' => []
    ];

    /**
     * Fetches array of type request in global request data array.
     * 
     * @param string $requestType name of type request.
     **/ 
    public function __construct(private string $requestType)
    {
        if (!isset(self::$requestData['_request'][$requestType])) {
            self::$requestData['_request'][$requestType] = $this->fetchRequestData();
        }
    }

    private function fetchRequestData(): array
    {
        return match ($this->requestType) {
            '_get' => $_GET,
            '_post' => $_POST,
            default => [],
        };
    }

    public function get(string $key, mixed $defaultFallback = null): mixed
    {
        return self::$requestData['_request'][$this->requestType][$key] ?? $defaultFallback;
    }

    public function set(string $key, mixed $value): void
    {
        self::$requestData['_request'][$this->requestType][$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset(self::$requestData['_request'][$this->requestType][$key]);
    }

    public function all(): array|null
    {
        return self::$requestData['_request'][$this->requestType] ?? [];
    }

    public function remove(string $key): void
    {
        unset(self::$requestData['_request'][$this->requestType][$key]);
    }

    public function getDataSet(string $needle, bool $trimNeedle = false): array|null
    {
        if(isset(self::$requestData['_request'][$this->requestType]) && !empty(self::$requestData['_request'][$this->requestType])) {
            $filteredData = array_filter(self::$requestData['_request'][$this->requestType],
                function ($key) use ($needle) {
                    return str_contains($key, $needle);
                }, ARRAY_FILTER_USE_KEY);

            return $trimNeedle ? \App\Utils::subTrimArrayKeys($needle, $filteredData) : $filteredData;
        }

        return [];
    }
}