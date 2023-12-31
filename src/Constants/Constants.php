<?php

declare(strict_types=1);

namespace App\Constants;

class Constants
{
    /**
     * The default currency used for operations.
     */
    public const DEFAULT_CURRENCY = 'EUR';

    /**
     * Business client withdrawal fee rate.
     */
    public const BUSINESS_WITHDRAW_FEE = 0.005;

    /**
     * Deposit fee rate.
     */
    public const DEPOSIT_FEE = 0.0003;

    /**
     * Private client commission rate for withdrawals.
     */
    public const PRIVATE_COMMISSION_RATE = 0.003;
    /**
     * Free withdrawal amount limit for private accounts in EUR
     */
    public const PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT = 1000.00;

    /**
     * Number of free withdrawals for private accounts
     */
    public const PRIVATE_FREE_WITHDRAW_COUNT = 3;
    /**
     * Number of decimal places for each currency.
     */
    public const CURRENCY_DECIMALS = [
        'JPY' => 0,
        'USD' => 2,
        'EUR' => 2
    ];

    /**
     * The number of decimals to use for financial calculations.
     */
    public const DECIMALS_NUMBER = 2;
    /**
     * The URL for fetching exchange rates.
     */
    public const EXCHANGE_RATE_API_URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    /**
     * The scale to use for arbitrary precision arithmetic functions.
     */
    public const BC_SCALE = 10;
}
