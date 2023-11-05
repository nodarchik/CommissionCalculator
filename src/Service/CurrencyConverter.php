<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\ApiClient;
use App\Constants\Constants;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyConverter
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws GuzzleException
     */
    public function convertAmountToDefaultCurrency(float $amount, string $currency): float
    {
        $exchangeRates = $this->fetchExchangeRates();

        if ($currency === Constants::DEFAULT_CURRENCY || !isset($exchangeRates[$currency])) {
            return $amount;
        }

        // Use bc_math for precise division
        $result = bcdiv((string)$amount, (string)$exchangeRates[$currency], Constants::BC_SCALE);

        return floatval($result);  // Convert back to float for compatibility
    }

    /**
     * @throws GuzzleException
     */
    public function convertAmountFromDefaultCurrency(float $amount, string $currency): float
    {
        $exchangeRates = $this->fetchExchangeRates();

        if ($currency === Constants::DEFAULT_CURRENCY || !isset($exchangeRates[$currency])) {
            return $amount;
        }

        // Use bc_math for precise multiplication
        $result = bcmul((string)$amount, (string)$exchangeRates[$currency], Constants::BC_SCALE);

        return floatval($result);  // Convert back to float for compatibility
    }

    /**
     * @throws GuzzleException
     */
    private function fetchExchangeRates(): array
    {
        $exchangeRateData = $this->apiClient->fetchExchangeRates();
        return $exchangeRateData['rates'] ?? [];
    }
}