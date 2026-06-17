<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\OrderSummary;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers OrderSummary
 */
class OrderSummaryTest extends TestCase
{
    public function testFromArrayPopulatesAllFields(): void
    {
        $summary = OrderSummary::fromArray([
            'orderId' => 'ORD-001',
            'amount' => '1000',
            'currency' => 'EUR',
            'customerId' => 'CUST-001',
            'description' => 'Ordine test',
            'customField' => 'promo2024',
            'authorizedAmount' => '1000',
            'capturedAmount' => '0',
            'lastOperationType' => 'AUTHORIZATION',
            'lastOperationTime' => '2024-01-01T12:00:00.000Z',
            'termsAndConditionsIds' => ['uuid-1111', 'uuid-2222'],
        ]);

        $this->assertSame('ORD-001', $summary->getOrderId());
        $this->assertSame('1000', $summary->getAmount());
        $this->assertSame('EUR', $summary->getCurrency());
        $this->assertSame('CUST-001', $summary->getCustomerId());
        $this->assertSame('Ordine test', $summary->getDescription());
        $this->assertSame('promo2024', $summary->getCustomField());
        $this->assertSame('1000', $summary->getAuthorizedAmount());
        $this->assertSame('0', $summary->getCapturedAmount());
        $this->assertSame('AUTHORIZATION', $summary->getLastOperationType());
        $this->assertSame('2024-01-01T12:00:00.000Z', $summary->getLastOperationTime());
        $this->assertSame(['uuid-1111', 'uuid-2222'], $summary->getTermsAndConditionsIds());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $summary = OrderSummary::fromArray([]);

        $this->assertNull($summary->getOrderId());
        $this->assertNull($summary->getAmount());
        $this->assertNull($summary->getCurrency());
        $this->assertNull($summary->getCustomerId());
        $this->assertNull($summary->getDescription());
        $this->assertNull($summary->getCustomField());
        $this->assertNull($summary->getAuthorizedAmount());
        $this->assertNull($summary->getCapturedAmount());
        $this->assertNull($summary->getLastOperationType());
        $this->assertNull($summary->getLastOperationTime());
        $this->assertNull($summary->getTermsAndConditionsIds());
    }

    public function testFromArrayCastsIntegerAmountToString(): void
    {
        $summary = OrderSummary::fromArray(['amount' => 1000]);

        $this->assertSame('1000', $summary->getAmount());
    }
}
