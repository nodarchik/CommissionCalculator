<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CommissionCalculator\WithdrawPrivateCommissionCalculator;
use App\Service\CurrencyConverter;
use App\Service\MathService;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WithdrawPrivateCommissionCalculatorTest extends TestCase
{
    private TransactionRepository|MockObject $transactionRepositoryMock;
    private MockObject|CurrencyConverter $currencyConverterMock;
    private MathService|MockObject $mathServiceMock;
    private WithdrawPrivateCommissionCalculator $calculator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        // Create mock objects for the dependencies
        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);
        $this->currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $this->mathServiceMock = $this->createMock(MathService::class);

        // Instantiate the service with mock dependencies
        $this->calculator = new WithdrawPrivateCommissionCalculator(
            $this->transactionRepositoryMock,
            $this->currencyConverterMock,
            $this->mathServiceMock
        );
    }

    public function testInitialization()
    {
        $this->assertInstanceOf(
            WithdrawPrivateCommissionCalculator::class,
            $this->calculator,
            'The calculator should be an instance of WithdrawPrivateCommissionCalculator.'
        );
    }

    /**
     * @throws GuzzleException
     */
    public function testCalculateCommissionForFirstFreeWithdrawal()
    {
        // Assuming Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT is 1000
        // Configure the stubs with expected behavior
        $this->transactionRepositoryMock->method('getTransactionsForUserInWeek')
            ->willReturn([]);
        $this->currencyConverterMock->method('convertAmountToDefaultCurrency')
            ->willReturn(100.0);
        $this->mathServiceMock->method('bcRoundUp')
            ->willReturn('0.00');

        $transaction = new Transaction(1, 'private', 'withdraw', 100.0, 'EUR', new DateTime('this week'));

        $commission = $this->calculator->calculate($transaction);

        $this->assertEquals('0.00', $commission, 'The commission should be zero.');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testCalculateCommissionWithNonDefaultCurrency()
    {
        // Assuming the user has already made withdrawals that reach the free limit
        $this->transactionRepositoryMock->method('getTransactionsForUserInWeek')
            ->willReturn([$this->createMock(Transaction::class)]);

        // Simulate a currency conversion from USD to EUR
        $this->currencyConverterMock->method('convertAmountToDefaultCurrency')
            ->willReturn(900.0); // The converted amount in EUR

        // Simulate a currency conversion from EUR to USD for the fee amount
        $this->currencyConverterMock->method('convertAmountFromDefaultCurrency')
            ->willReturn(1.1); // The converted amount in USD

        $this->mathServiceMock->method('bcRoundUp')
            ->willReturn('1.10'); // Simulate rounding up to 2 decimal places

        $transaction = new Transaction(1, 'private', 'withdraw', 1000.0, 'USD', new DateTime('this week'));

        $commission = $this->calculator->calculate($transaction);

        $this->assertEquals('1.10', $commission, 'The commission should converted for a non-default currency.');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testResetCountersBetweenTransactions()
    {
        // Simulate two transactions in the same week
        $transactions = [
            $this->createMock(Transaction::class),
            $this->createMock(Transaction::class)
        ];

        $this->transactionRepositoryMock->method('getTransactionsForUserInWeek')
            ->willReturnOnConsecutiveCalls($transactions, []);

        $transaction1 = new Transaction(1, 'private', 'withdraw', 100.0, 'EUR', new DateTime('this week'));
        $transaction2 = new Transaction(1, 'private', 'withdraw', 100.0, 'EUR', new DateTime('this week'));

        // Calculate commission for the first transaction
        $this->calculator->calculate($transaction1);
        // Calculate commission for the second transaction
        $commission = $this->calculator->calculate($transaction2);

        // The commission for the second transaction should be treated independently
        $this->assertEquals('0.00', $commission, 'The counters should be reset between transactions.');
    }
}
