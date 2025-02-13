<?php namespace App\Requests;

abstract class Request implements IRequest
{
    /**
     * The global request data array.
     **/ 
    protected array $requestData = [
        '_request' => []
    ];

    /**
     * Creates array of type request in global request data array.
     * 
     * @param string $requestType name of type request.
     **/ 
    public function __construct(private string $requestType)
    {
        if (!isset($this->requestData['_request'][$requestType])) {
            $this->requestData['_request'][$requestType] = $this->fetchRequestData();
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
        return $this->requestData['_request'][$this->requestType][$key] ?? $defaultFallback;
    }

    public function has(string $key): bool
    {
        return isset($this->requestData['_request'][$this->requestType][$key]);
    }

    public function all(): array|null
    {
        return $this->requestData['_request'][$this->requestType] ?? [];
    }

    public function getDataSet(string $needle, bool $trimNeedle = false): array|null
    {
        if(isset($this->requestData['_request'][$this->requestType]) && !empty($this->requestData['_request'][$this->requestType])) {
            $filteredData = array_filter($this->requestData['_request'][$this->requestType],
                function ($key) use ($needle) {
                    return str_contains($key, $needle);
                }, ARRAY_FILTER_USE_KEY);

            return $trimNeedle ? \App\Utils::subTrimArrayKeys($needle, $filteredData) : $filteredData;
        }

        return [];
    }
}