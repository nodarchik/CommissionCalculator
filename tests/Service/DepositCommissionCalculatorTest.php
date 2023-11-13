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
        $this->mathServiceMock = $this->createMock(MathService::class);
        $this->calculator = new DepositCommissionCalculator($this->mathServiceMock);
    }

    public function testCalculateDepositCommission()
    {
        $transaction = new Transaction(
            1,
            'private',
            'deposit',
            200.0,
            'EUR',
            new DateTime('now')
        );
        $expectedFee = '0.06';
        $this->mathServiceMock->method('bcRoundUp')->willReturn($expectedFee);
        $commission = $this->calculator->calculate($transaction);
        $this->assertEquals($expectedFee, $commission, 'The calculated commission should match the expected fee.');
    }
}
