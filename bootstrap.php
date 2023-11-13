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

require 'vendor/autoload.php';

$apiClient = new ApiClient(new GuzzleHttp\Client());
$currencyConverter = new CurrencyConverter($apiClient);
$transactionRepository = new TransactionRepository();
$mathService = new MathService();
$depositCalculator = new DepositCommissionCalculator($mathService);
$withdrawPrivateCalculator = new WithdrawPrivateCommissionCalculator($transactionRepository, $currencyConverter, $mathService);
$withdrawBusinessCalculator = new WithdrawBusinessCommissionCalculator($mathService);

$transactionService = new TransactionService(
    $depositCalculator,
    $withdrawPrivateCalculator,
    $withdrawBusinessCalculator,
    $currencyConverter,
    $transactionRepository
);
$csvReader = new CSVReader();
