<?php namespace App;

class Uri {

    use \App\Cleaners\Sanitizer;

    /**
     * Constructor for initializing the Uri class with URL-related data.
     *
     * @param bool $secured Indicates if the connection is secured.
     * @param string $protocol The protocol used for the URL.
     * @param string $baseUrl The base URL, typically the host/domain.
     * @param string $path The URL path.
     * @param string $currentUrl The full current URL.
     * @param array $pathSegments An array of path segments.
     * @param array $magicGet Custom GET parameters, if any exists.
     **/
    public function __construct(
        private bool $secured = false,
        private string $protocol = '',
        private string $baseUrl = '',
        private string $path = '',
        private string $currentUrl = '',
        private array $pathSegments = [],
        private array $magicGet = []
    )
    {
        $getRequest = new Requests\GetRequest;
        
        $this->secured = isset($_SERVER['HTTPS']);
        $this->protocol = $this->secured ? 'https' : 'http' . '://';
        $this->baseUrl = $_SERVER['HTTP_HOST'];
        $this->path = '/' . $getRequest->get('_url');
        $this->currentUrl = $this->protocol . $this->baseUrl . $this->path;

        $this->pathSegments = explode('/', ltrim($this->path, '/'));

        $getData = $getRequest->all();
        $this->magicGet = array_map(function ($dirtyMagicGetItem) {
            return $this->sanitize($dirtyMagicGetItem);
        }, array_slice($getData, 0, array_search('_url', array_keys($getData))));
    }

    /**
     * Checks if current Uri is secured.
     **/
    public function isSecured(): bool
    {
        return $this->secured;
    }

    /**
     * Get the absolute URL of current Uri.
     **/ 
    public function getCurrentUrl(): string
    {
        return $this->currentUrl;
    }

    /**
     * Get the base of current Uri.
     **/ 
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the path of current Uri.
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the segments of current Uri.
     **/ 
    public function geSegments(): array
    {
        return $this->pathSegments;
    }
}