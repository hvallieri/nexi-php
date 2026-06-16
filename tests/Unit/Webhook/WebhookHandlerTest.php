<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Webhook;

use Hval\Nexi\Exception\WebhookSignatureException;
use Hval\Nexi\Webhook\WebhookHandler;
use Hval\Nexi\Webhook\WebhookNotification;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers WebhookHandler
 * @covers WebhookNotification
 */
class WebhookHandlerTest extends TestCase
{
    private const TOKEN = 'securetoken123abc';

    /** @var WebhookHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->handler = new WebhookHandler();
    }

    /** @return array<string, mixed> */
    private function makePayload(string $operationResult, string $operationType = 'AUTHORIZATION'): array
    {
        return [
            'eventId' => 'EVT-001',
            'eventTime' => '2024-01-01T12:00:00.000Z',
            'securityToken' => self::TOKEN,
            'operation' => [
                'orderId' => 'ORD-001',
                'operationId' => 'OP-999',
                'channel' => 'ECOMMERCE',
                'operationType' => $operationType,
                'operationResult' => $operationResult,
                'operationTime' => '2024-01-01T12:00:00.000Z',
                'paymentMethod' => 'CARD',
                'paymentCircuit' => 'VISA',
                'operationAmount' => '1000',
                'operationCurrency' => 'EUR',
            ],
        ];
    }

    public function testHandleReturnsNotificationWhenTokenMatches(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('AUTHORIZED')), self::TOKEN);

        $this->assertInstanceOf(WebhookNotification::class, $notification);
        $this->assertSame('EVT-001', $notification->getEventId());
        $this->assertSame('ORD-001', $notification->getOrderId());
        $this->assertSame('OP-999', $notification->getOperationId());
        $this->assertSame('AUTHORIZATION', $notification->getOperationType());
        $this->assertSame('AUTHORIZED', $notification->getOperationResult());
        $this->assertSame('ECOMMERCE', $notification->getChannel());
        $this->assertSame('CARD', $notification->getPaymentMethod());
        $this->assertSame('VISA', $notification->getPaymentCircuit());
        $this->assertSame('1000', $notification->getOperationAmount());
        $this->assertSame('EUR', $notification->getOperationCurrency());
        $this->assertTrue($notification->isAuthorized());
    }

    public function testHandleThrowsOnTokenMismatch(): void
    {
        $payload = json_encode([
            'securityToken' => 'wrong-token',
            'operation' => ['orderId' => 'ORD-001'],
        ]);

        $this->expectException(WebhookSignatureException::class);
        $this->expectExceptionMessage('securityToken mismatch');

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testHandleThrowsOnInvalidJson(): void
    {
        $this->expectException(WebhookSignatureException::class);
        $this->expectExceptionMessage('not valid JSON');

        $this->handler->handle('not-valid-json{{{', self::TOKEN);
    }

    public function testHandleThrowsWhenSecurityTokenMissingFromPayload(): void
    {
        $payload = json_encode([
            'operation' => ['orderId' => 'ORD-001', 'operationResult' => 'AUTHORIZED'],
        ]);

        $this->expectException(WebhookSignatureException::class);

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testIsAuthorizedReturnsFalseForDeclined(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('DECLINED')), self::TOKEN);

        $this->assertFalse($notification->isAuthorized());
    }

    public function testIsExecutedReturnsTrueForExecuted(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('EXECUTED', 'PAYMENT')), self::TOKEN);

        $this->assertTrue($notification->isExecuted());
        $this->assertFalse($notification->isAuthorized());
    }

    public function testIsAuthorizedReturnsFalseForExecuted(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('EXECUTED', 'PAYMENT')), self::TOKEN);

        $this->assertFalse($notification->isAuthorized());
    }

    public function testHandleTokenComparisonIsCaseSensitive(): void
    {
        $payload = json_encode([
            'securityToken' => strtoupper(self::TOKEN),
            'operation' => ['orderId' => 'ORD-001'],
        ]);

        $this->expectException(WebhookSignatureException::class);

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testHandleWithEmptyTokenThrows(): void
    {
        $payload = json_encode([
            'securityToken' => '',
            'operation' => ['orderId' => 'ORD-001'],
        ]);

        $this->expectException(WebhookSignatureException::class);

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testRawDataIsPreservedOnNotification(): void
    {
        $data = $this->makePayload('AUTHORIZED');
        $data['customField'] = 'extra-data';

        $notification = $this->handler->handle(json_encode($data), self::TOKEN);

        $this->assertSame('extra-data', $notification->getRaw()['customField']);
    }

    public function testRootLevelFieldsAreParsed(): void
    {
        $data = $this->makePayload('AUTHORIZED');
        $data['paymentId'] = 'PAY-001';
        $data['result'] = 'AUTHORIZED';
        $data['paymentMethod'] = 'CARD';
        $data['paymentInstrumentInfo'] = 'VISA ****1234';
        $data['orderAmount'] = '1000';
        $data['currency'] = 'EUR';
        $data['customerId'] = 'CUST-001';
        $data['description'] = 'Ordine test';
        $data['customField'] = 'promo2024';
        $data['orderTime'] = '2024-01-01T12:00:00.000Z';
        $data['eventType'] = 'PAYMENT_RESULT';
        $data['errorCode'] = null;
        $data['errorMessage'] = null;

        $notification = $this->handler->handle(json_encode($data), self::TOKEN);

        $this->assertSame('PAY-001', $notification->getPaymentId());
        $this->assertSame('AUTHORIZED', $notification->getResult());
        $this->assertSame('CARD', $notification->getRootPaymentMethod());
        $this->assertSame('VISA ****1234', $notification->getRootPaymentInstrumentInfo());
        $this->assertSame('1000', $notification->getOrderAmount());
        $this->assertSame('EUR', $notification->getCurrency());
        $this->assertSame('CUST-001', $notification->getCustomerId());
        $this->assertSame('Ordine test', $notification->getDescription());
        $this->assertSame('promo2024', $notification->getCustomField());
        $this->assertSame('2024-01-01T12:00:00.000Z', $notification->getOrderTime());
        $this->assertSame('PAYMENT_RESULT', $notification->getEventType());
        $this->assertNull($notification->getErrorCode());
        $this->assertNull($notification->getErrorMessage());
    }

    public function testOperationExtendedFieldsAreParsed(): void
    {
        $data = $this->makePayload('AUTHORIZED');
        $data['operation']['paymentInstrumentInfo'] = 'VISA ****1234';
        $data['operation']['paymentEndToEndId'] = 'E2E-001';
        $data['operation']['cancelledOperationId'] = null;
        $data['operation']['warnings'] = [['code' => 'WARN01', 'description' => 'test']];
        $data['operation']['paymentLinkId'] = 'LINK-001';
        $data['operation']['terminalId'] = 'TERM-001';

        $notification = $this->handler->handle(json_encode($data), self::TOKEN);

        $this->assertSame('VISA ****1234', $notification->getPaymentInstrumentInfo());
        $this->assertSame('E2E-001', $notification->getPaymentEndToEndId());
        $this->assertNull($notification->getCancelledOperationId());
        $this->assertCount(1, $notification->getWarnings());
        $this->assertSame('LINK-001', $notification->getPaymentLinkId());
        $this->assertSame('TERM-001', $notification->getTerminalId());
    }

    public function testWarningsDefaultsToEmptyArray(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('AUTHORIZED')), self::TOKEN);

        $this->assertSame([], $notification->getWarnings());
    }

    public function testAdditionalDataIsParsedWhenPresent(): void
    {
        $data = $this->makePayload('AUTHORIZED');
        $data['additionalData'] = [
            'status' => 'AUTHORIZED',
            'authorizationCode' => 'AUTH123',
            'maskedPan' => '************1234',
            'threeDS' => 'FULL_SECURE',
            'schemaTID' => 'TID789',
        ];

        $notification = $this->handler->handle(json_encode($data), self::TOKEN);

        $additional = $notification->getAdditionalData();
        $this->assertNotNull($additional);
        $this->assertSame('AUTHORIZED', $additional->getStatus());
        $this->assertSame('AUTH123', $additional->getAuthorizationCode());
        $this->assertSame('************1234', $additional->getMaskedPan());
        $this->assertSame('FULL_SECURE', $additional->getThreeDS());
        $this->assertSame('TID789', $additional->getSchemaTID());
    }

    public function testAdditionalDataIsNullWhenAbsent(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('AUTHORIZED')), self::TOKEN);

        $this->assertNull($notification->getAdditionalData());
    }

    public function testNewRootFieldsReturnNullWhenAbsent(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('AUTHORIZED')), self::TOKEN);

        $this->assertNull($notification->getPaymentId());
        $this->assertNull($notification->getResult());
        $this->assertNull($notification->getRootPaymentMethod());
        $this->assertNull($notification->getRootPaymentInstrumentInfo());
        $this->assertNull($notification->getOrderAmount());
        $this->assertNull($notification->getCurrency());
        $this->assertNull($notification->getCustomerId());
        $this->assertNull($notification->getDescription());
        $this->assertNull($notification->getCustomField());
        $this->assertNull($notification->getOrderTime());
        $this->assertNull($notification->getEventType());
        $this->assertNull($notification->getErrorCode());
        $this->assertNull($notification->getErrorMessage());
    }

    public function testMissingOperationFieldsReturnNull(): void
    {
        $payload = json_encode([
            'securityToken' => self::TOKEN,
            'operation' => [],
        ]);

        $notification = $this->handler->handle($payload, self::TOKEN);

        $this->assertNull($notification->getOrderId());
        $this->assertNull($notification->getOperationResult());
        $this->assertFalse($notification->isAuthorized());
    }

    public function testMissingOperationKeyReturnsNullFields(): void
    {
        $payload = json_encode(['securityToken' => self::TOKEN]);

        $notification = $this->handler->handle($payload, self::TOKEN);

        $this->assertNull($notification->getOrderId());
        $this->assertNull($notification->getOperationId());
        $this->assertNull($notification->getOperationType());
        $this->assertNull($notification->getOperationResult());
        $this->assertFalse($notification->isAuthorized());
    }

    public function testSecurityTokenIsAccessibleOnNotification(): void
    {
        $notification = $this->handler->handle(json_encode($this->makePayload('AUTHORIZED')), self::TOKEN);

        $this->assertSame(self::TOKEN, $notification->getSecurityToken());
    }
}
