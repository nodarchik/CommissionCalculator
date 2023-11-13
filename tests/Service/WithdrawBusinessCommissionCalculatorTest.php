<?php

declare(strict_types=1);

namespace Tests\Service;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Service\CommissionCalculator\WithdrawBusinessCommissionCalculator;
use App\Model\Transaction;
use App\Service\MathService;
use App\Constants\Constants;
use DateTime;

class WithdrawBusinessCommissionCalculatorTest extends TestCase
{
    private MathService|MockObject $mathServiceMock;
    private WithdrawBusinessCommissionCalculator $calculator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mathServiceMock = $this->createMock(MathService::class);
        $this->calculator = new WithdrawBusinessCommissionCalculator($this->mathServiceMock);
    }

    public function testCalculateBusinessWithdrawCommission()
    {
        $transaction = new Transaction(
            1,
            'business',
            'withdraw',
            1000.0,
            'EUR',
            new DateTime('now')
        );
        $expectedFee = '5.00';
        $this->mathServiceMock->method('bcRoundUp')->willReturn($expectedFee);
        $commission = $this->calculator->calculate($transaction);
        $this->assertEquals($expectedFee, $commission, 'The calculated commission should match the expected fee.');
    }
}
