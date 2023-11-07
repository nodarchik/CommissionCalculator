<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Client\ApiClient;
use App\Constants\Constants;
use App\Service\CurrencyConverter;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    private MockObject|ApiClient $apiClientMock;
    private CurrencyConverter $currencyConverter;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        // Create a mock for the ApiClient dependency.
        $this->apiClientMock = $this->createMock(ApiClient::class);

        // Instantiate the service with the mocked ApiClient.
        $this->currencyConverter = new CurrencyConverter($this->apiClientMock);
    }

    /**
     * @throws GuzzleException
     */
    public function testConvertToDefaultCurrencyWithDifferentCurrency()
    {
        // Define a test amount and a non-default currency.
        $amount = 100.0;
        $currency = 'USD';
        $exchangeRate = 1.2; // Assuming 1 USD = 0.83 EUR (approximate)

        // Mock the ApiClient to return a predefined exchange rate.
        $this->apiClientMock->method('fetchExchangeRates')
            ->willReturn(['rates' => [$currency => $exchangeRate]]);

        $convertedAmount = $this->currencyConverter->convertAmountToDefaultCurrency($amount, $currency);

        // Calculate the expected amount using bcdiv for precision.
        $expectedAmount = bcdiv((string)$amount, (string)$exchangeRate, Constants::BC_SCALE);

        $this->assertEquals(floatval($expectedAmount), $convertedAmount, 'The amount should be correctly converted');
    }
}
