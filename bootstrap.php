<?php

use App\Client\ApiClient;
use App\Repository\TransactionRepository;
use App\Service\CommissionCalculator\DepositCommissionCalculator;
use App\Service\CommissionCalculator\WithdrawBusinessCommissionCalculator;
use App\Service\CommissionCalculator\WithdrawPrivateCommissionCalculator;
use App\Service\CurrencyConverter;
use App\Service\MathService;
use App\Service\TransactionService;
use App\Utils\CSVReader;
use App\Utils\CSVWriter;

require 'vendor/autoload.php';

// Initialize API client
$apiClient = new ApiClient(new GuzzleHttp\Client());

// Initialize currency converter
$currencyConverter = new CurrencyConverter($apiClient);

// Initialize repositories
$transactionRepository = new TransactionRepository();

// Instantiate the MathService
$mathService = new MathService();

// Initialize commission calculators
$depositCalculator = new DepositCommissionCalculator($mathService);
$withdrawPrivateCalculator = new WithdrawPrivateCommissionCalculator($transactionRepository, $currencyConverter, $mathService);
$withdrawBusinessCalculator = new WithdrawBusinessCommissionCalculator($mathService);

// Initialize transaction service
$transactionService = new TransactionService(
    $depositCalculator,
    $withdrawPrivateCalculator,
    $withdrawBusinessCalculator,
    $currencyConverter,
    $transactionRepository
);

// Initialize CSV utilities
$csvReader = new CSVReader();
$csvWriter = new CSVWriter();
