<?php

require 'bootstrap.php';

use App\Controller\TransactionController;

if (empty($argv[1])) {
    die("Please provide the input file name as the first argument.\n");
}

$inputFilePath = $argv[1];
$transactionController = new TransactionController($transactionService, $csvReader);
$transactionController->processTransactions($inputFilePath);
