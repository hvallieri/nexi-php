<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\OperationDetails;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers OperationDetails
 */
class OperationDetailsTest extends TestCase
{
    public function testFromArrayPopulatesAllFields(): void
    {
        $details = OperationDetails::fromArray([
            'orderId' => 'ORD-001',
            'operationId' => 'OP-001',
            'channel' => 'ECOMMERCE',
            'channelDetail' => 'HOSTED_PAYMENT_PAGE',
            'operationType' => 'AUTHORIZATION',
            'operationResult' => 'AUTHORIZED',
            'operationTime' => '2024-01-01T12:00:00.000Z',
            'paymentMethod' => 'CARD',
            'paymentCircuit' => 'VISA',
            'paymentInstrumentInfo' => '************1234',
            'paymentEndToEndId' => 'E2E-001',
            'cancelledOperationId' => null,
            'operationAmount' => '1000',
            'operationCurrency' => 'EUR',
            'paymentLinkId' => null,
            'terminalId' => 'TERM-001',
            'warnings' => [['code' => 'W01', 'description' => 'test']],
        ]);

        $this->assertSame('ORD-001', $details->getOrderId());
        $this->assertSame('OP-001', $details->getOperationId());
        $this->assertSame('ECOMMERCE', $details->getChannel());
        $this->assertSame('HOSTED_PAYMENT_PAGE', $details->getChannelDetail());
        $this->assertSame('AUTHORIZATION', $details->getOperationType());
        $this->assertSame('AUTHORIZED', $details->getOperationResult());
        $this->assertSame('2024-01-01T12:00:00.000Z', $details->getOperationTime());
        $this->assertSame('CARD', $details->getPaymentMethod());
        $this->assertSame('VISA', $details->getPaymentCircuit());
        $this->assertSame('************1234', $details->getPaymentInstrumentInfo());
        $this->assertSame('E2E-001', $details->getPaymentEndToEndId());
        $this->assertNull($details->getCancelledOperationId());
        $this->assertSame('1000', $details->getOperationAmount());
        $this->assertSame('EUR', $details->getOperationCurrency());
        $this->assertNull($details->getPaymentLinkId());
        $this->assertSame('TERM-001', $details->getTerminalId());
        $this->assertCount(1, $details->getWarnings());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $details = OperationDetails::fromArray([]);

        $this->assertNull($details->getOrderId());
        $this->assertNull($details->getOperationId());
        $this->assertNull($details->getChannel());
        $this->assertNull($details->getChannelDetail());
        $this->assertNull($details->getOperationType());
        $this->assertNull($details->getOperationResult());
        $this->assertNull($details->getOperationTime());
        $this->assertNull($details->getPaymentMethod());
        $this->assertNull($details->getPaymentCircuit());
        $this->assertNull($details->getPaymentInstrumentInfo());
        $this->assertNull($details->getPaymentEndToEndId());
        $this->assertNull($details->getCancelledOperationId());
        $this->assertNull($details->getOperationAmount());
        $this->assertNull($details->getOperationCurrency());
        $this->assertNull($details->getPaymentLinkId());
        $this->assertNull($details->getTerminalId());
        $this->assertSame([], $details->getWarnings());
    }

    public function testConstants(): void
    {
        $this->assertSame('ECOMMERCE', OperationDetails::CHANNEL_ECOMMERCE);
        $this->assertSame('POS', OperationDetails::CHANNEL_POS);
        $this->assertSame('BACKOFFICE', OperationDetails::CHANNEL_BACKOFFICE);
        $this->assertSame('AUTHORIZATION', OperationDetails::OPERATION_TYPE_AUTHORIZATION);
        $this->assertSame('CAPTURE', OperationDetails::OPERATION_TYPE_CAPTURE);
        $this->assertSame('VOID', OperationDetails::OPERATION_TYPE_VOID);
        $this->assertSame('REFUND', OperationDetails::OPERATION_TYPE_REFUND);
        $this->assertSame('CANCEL', OperationDetails::OPERATION_TYPE_CANCEL);
    }
}
