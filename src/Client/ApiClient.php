<?php

declare(strict_types=1);

namespace App\Client;

use GuzzleHttp\Client;
use App\Constants\Constants;
use GuzzleHttp\Exception\GuzzleException;

class ApiClient
{
    private Client $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws GuzzleException
     */
    public function fetchExchangeRates(): array
    {
        $response = $this->httpClient->get(Constants::EXCHANGE_RATE_API_URL);
        return json_decode($response->getBody()->getContents(), true);
    }
}
