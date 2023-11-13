<?php

declare(strict_types=1);

namespace App\Client;

use GuzzleHttp\Client;
use App\Constants\Constants;
use GuzzleHttp\Exception\GuzzleException;

class ApiClient
{
    private Client $httpClient;

    private static ?array $exchangeRatesCache = null;
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws GuzzleException
     */
    public function fetchExchangeRates(): array
    {
        if (self::$exchangeRatesCache !== null) {
            return self::$exchangeRatesCache;
        }
        $response = $this->httpClient->get(Constants::EXCHANGE_RATE_API_URL);
        self::$exchangeRatesCache = json_decode($response->getBody()->getContents(), true);
        return self::$exchangeRatesCache;
    }
}
