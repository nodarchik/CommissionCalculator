<?php

use App\Controller\TransactionController;

require 'bootstrap.php';

// Check if the input file name is provided
if (empty($argv[1])) {
    die("Please provide the input file name as the first argument.\n");
}

$inputFilePath = $argv[1];
$outputFilePath = 'output.csv';

// Instantiate the TransactionController
$transactionController = new TransactionController($transactionService, $csvReader, $csvWriter);

// Process transactions using the TransactionController
$transactionController->processTransactions($inputFilePath, $outputFilePath);
