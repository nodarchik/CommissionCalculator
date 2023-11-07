<?php

// Strict types declaration for type safety.
declare(strict_types=1);

// Namespace for the ApiClient class.
namespace App\Client;

// Importing the HTTP client and exception handling.
use GuzzleHttp\Client;
use App\Constants\Constants;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Client for making API calls to fetch exchange rates.
 */
class ApiClient
{
    /**
     * HTTP client for sending requests.
     * @var Client
     */
    private Client $httpClient;

    /**
     * Constructor to inject the HTTP client.
     *
     * @param Client $httpClient Client instance for making HTTP calls.
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Fetches exchange rates from an API.
     *
     * @throws GuzzleException If there is a problem with the HTTP client request.
     * @return array The exchange rates data as an associative array.
     */
    public function fetchExchangeRates(): array
    {
        // Making a GET request to the exchange rate API.
        $response = $this->httpClient->get(Constants::EXCHANGE_RATE_API_URL);
        // Decoding the JSON response into an associative array.
        return json_decode($response->getBody()->getContents(), true);
    }
}
