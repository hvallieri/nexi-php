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
