<?php

require 'vendor/autoload.php';  // Make sure to include the Composer autoloader

// Initialize your components (you'd normally do this in a bootstrap file)
$apiClient = new App\Client\ApiClient(new GuzzleHttp\Client());
$currencyConverter = new App\Service\CurrencyConverter($apiClient);

$transactionRepository = new App\Repository\TransactionRepository();
$accountRepository = new App\Repository\AccountRepository();

$depositCalculator = new App\Service\CommissionCalculator\DepositCommissionCalculator();
$withdrawPrivateCalculator = new App\Service\CommissionCalculator\WithdrawPrivateCommissionCalculator($transactionRepository, $currencyConverter);
$withdrawBusinessCalculator = new App\Service\CommissionCalculator\WithdrawBusinessCommissionCalculator();

$transactionService = new App\Service\TransactionService(
    $depositCalculator,
    $withdrawPrivateCalculator,
    $withdrawBusinessCalculator,
    $currencyConverter,
    $transactionRepository
);

$csvReader = new App\Utils\CSVReader();
$csvWriter = new App\Utils\CSVWriter();

// Read transactions from CSV
$filePath = 'input.csv';  // Your input CSV file path
$transactions = $csvReader->read($filePath);

// Process transactions and collect commissions
$commissions = [];
foreach ($transactions as $transaction) {
    $commission = $transactionService->processTransaction($transaction);
    $commissions[] = $commission;
}

// Write commissions to another CSV
$outputFilePath = 'output.csv';  // Your output CSV file path
$csvWriter->write($outputFilePath, $commissions);
