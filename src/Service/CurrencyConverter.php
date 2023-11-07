<?php

// Enforce strict typing mode for better type safety.
declare(strict_types=1);

// Define the namespace for the current class.
namespace App\Service;

// Import required classes and constants.
use App\Client\ApiClient;
use App\Constants\Constants;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Provides functionalities for currency conversion using exchange rates from an external API.
 */
class CurrencyConverter
{
    /** @var ApiClient */
    private ApiClient $apiClient;

    /**
     * Constructor to initialize the API client dependency.
     *
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Convert a given amount to the default currency.
     * If the currency is the default or not found in exchange rates, the original amount is returned.
     *
     * @param float  $amount   The amount to be converted.
     * @param string $currency The currency code of the amount.
     * @return float           The converted amount in the default currency.
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
     * Convert a given amount from the default currency to another currency.
     * If the target currency is the default or not found in exchange rates, the original amount is returned.
     *
     * @param float  $amount   The amount in the default currency to be converted.
     * @param string $currency The target currency code.
     * @return float           The converted amount in the target currency.
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
     * Fetch exchange rates using the API client.
     * Returns an associative array where keys are currency codes and values are exchange rates.
     *
     * @return array            The exchange rates data.
     * @throws GuzzleException
     */
    private function fetchExchangeRates(): array
    {
        $exchangeRateData = $this->apiClient->fetchExchangeRates();
        return $exchangeRateData['rates'] ?? [];
    }
}
