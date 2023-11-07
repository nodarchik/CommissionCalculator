<?php

namespace Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use App\Service\CommissionCalculator\WithdrawPrivateCommissionCalculator;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;
use App\Service\MathService;
use App\Model\Transaction;
use App\Constants\Constants;

class WithdrawPrivateCommissionCalculatorTest extends TestCase
{
    private $transactionRepositoryMock;
    private $currencyConverterMock;
    private $mathServiceMock;
    private $calculator;

    protected function setUp(): void
    {
        // Mock the TransactionRepository, CurrencyConverter, and MathService
        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);
        $this->currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $this->mathServiceMock = $this->createMock(MathService::class);

        // Create an instance of the calculator with the mocked dependencies
        $this->calculator = new WithdrawPrivateCommissionCalculator(
            $this->transactionRepositoryMock,
            $this->currencyConverterMock,
            $this->mathServiceMock
        );
    }


    public function testCalculateForFreeWithdrawal(): void
    {
        // Setup - Define the conditions for this test
        $transaction = new Transaction(
            1, // user id
            'private', // user type
            'withdraw', // operation type
            100.0, // amount
            'EUR', // currency
            new DateTime('2023-04-01') // date
        );

        // Define the behavior of the mocked methods for this test scenario
        $this->currencyConverterMock->method('convertAmountToDefaultCurrency')
            ->willReturn(100.0);

        $this->transactionRepositoryMock->method('getTransactionsForUserInWeek')
            ->willReturn([]);

        $this->mathServiceMock->method('bcRoundUp')
            ->willReturn('0.00');

        // Exercise - Call the method to test
        $result = $this->calculator->calculate($transaction);

        // Verify - Make assertions about the expected outcome
        $this->assertEquals('0.00', $result, 'The commission for a free withdrawal should be zero.');
    }

    public function testCalculateForWithdrawalInDifferentCurrencies(): void
    {
        // Setup - Define a transaction in a non-EUR currency
        $transaction = new Transaction(
            1, // user id
            'private', // user type
            'withdraw', // operation type
            100.0, // amount
            'USD', // currency
            new DateTime('2023-04-01') // date
        );

        // Define the behavior of the mocked methods for this scenario
        $this->currencyConverterMock->method('convertAmountToDefaultCurrency')
            ->willReturn(90.0);

        $this->transactionRepositoryMock->method('getTransactionsForUserInWeek')
            ->willReturn([]);

        $this->currencyConverterMock->method('convertAmountFromDefaultCurrency')
            ->willReturn(0.27);

        $this->mathServiceMock->method('bcRoundUp')
            ->willReturn('0.30');

        // Exercise - Call the method to test
        $result = $this->calculator->calculate($transaction);

        // Verify - Make assertions about the expected outcome
        $this->assertEquals('0.30', $result);
    }

    public function testCalculateForMultipleWithdrawalsAffectingLimit(): void
    {
        // Setup - Define a sequence of transactions
        $transaction1 = new Transaction(1, 'private', 'withdraw', 1000.0, 'EUR', new DateTime('2023-04-01'));
        $transaction2 = new Transaction(1, 'private', 'withdraw', 1000.0, 'EUR', new DateTime('2023-04-02'));

        $this->currencyConverterMock->method('convertAmountToDefaultCurrency')
            ->willReturnOnConsecutiveCalls(1000.0, 1000.0);

        $this->transactionRepositoryMock->method('getTransactionsForUserInWeek')
            ->willReturnOnConsecutiveCalls([$transaction1], [$transaction1, $transaction2]);

        $this->mathServiceMock->method('bcRoundUp')
            ->willReturnOnConsecutiveCalls('0.00', '3.00');

        // Exercise - Call the method to test for the first and second withdrawals
        $result1 = $this->calculator->calculate($transaction1);
        $result2 = $this->calculator->calculate($transaction2);

        // Verify - Make assertions about the expected outcome
        $this->assertEquals('0.00', $result1);
        $this->assertEquals('3.00', $result2);
    }

    public function testCalculateThrowsExceptionForInvalidCurrency(): void
    {
        // Setup - Define a transaction with an unsupported currency
        $transaction = new Transaction(1, 'private', 'withdraw', 100.0, 'XXX', new DateTime('2023-04-01'));

        $this->currencyConverterMock->method('convertAmountToDefaultCurrency')
            ->willThrowException(new Exception('Unsupported currency'));

        // Exercise and Verify - Expect an exception when calculate method is called
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported currency');

        $this->calculator->calculate($transaction);
    }
}
