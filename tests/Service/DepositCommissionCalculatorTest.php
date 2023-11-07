<?php

declare(strict_types=1);

namespace Tests\Service;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Service\CommissionCalculator\DepositCommissionCalculator;
use App\Model\Transaction;
use App\Service\MathService;
use App\Constants\Constants;
use DateTime;

class DepositCommissionCalculatorTest extends TestCase
{
    private MathService|MockObject $mathServiceMock;
    private DepositCommissionCalculator $calculator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        // Create a mock for the MathService dependency.
        $this->mathServiceMock = $this->createMock(MathService::class);

        // Instantiate the DepositCommissionCalculator with the mocked MathService.
        $this->calculator = new DepositCommissionCalculator($this->mathServiceMock);
    }

    public function testCalculateDepositCommission()
    {
        // Define a test transaction with known values.
        $transaction = new Transaction(
            1, // User ID
            'private', // User type
            'deposit', // Operation type
            200.0, // Amount
            'EUR', // Currency
            new DateTime('now') // Date
        );

        // Set the expected fee and configure the mock to return this fee.
        $expectedFee = '0.06'; // Assuming the deposit fee is 0.03% for easy math.
        $this->mathServiceMock->method('bcRoundUp')->willReturn($expectedFee);

        // Call the calculate method.
        $commission = $this->calculator->calculate($transaction);

        // Check that the commission is calculated as expected.
        $this->assertEquals($expectedFee, $commission, 'The calculated commission should match the expected fee.');
    }
}
