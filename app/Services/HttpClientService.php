<?php
namespace App\Services;

use GuzzleHttp\Client;

class HttpClientService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function fetchContent(string $url): string
    {
        $response = $this->httpClient->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Failed to fetch URL: $url");
        }

        return $response->getBody()->getContents();
    }
}
