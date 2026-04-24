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
    private const API_KEY = 'test-api-key-uuid';
    private const TOKEN = 'securetoken123abc';

    /** @var WebhookHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->handler = new WebhookHandler(self::API_KEY);
    }

    public function testHandleReturnsNotificationWhenTokenMatches(): void
    {
        $payload = json_encode([
            'orderId' => 'ORD-001',
            'operationId' => 'OP-999',
            'operationType' => 'AUTHORIZATION',
            'operationResult' => 'AUTHORIZED',
            'securityToken' => self::TOKEN,
        ]);

        $notification = $this->handler->handle($payload, self::TOKEN);

        $this->assertInstanceOf(WebhookNotification::class, $notification);
        $this->assertSame('ORD-001', $notification->getOrderId());
        $this->assertSame('OP-999', $notification->getOperationId());
        $this->assertSame('AUTHORIZATION', $notification->getOperationType());
        $this->assertSame('AUTHORIZED', $notification->getOperationResult());
        $this->assertTrue($notification->isAuthorized());
    }

    public function testHandleThrowsOnTokenMismatch(): void
    {
        $payload = json_encode([
            'orderId' => 'ORD-001',
            'securityToken' => 'wrong-token',
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
            'orderId' => 'ORD-001',
            'operationResult' => 'AUTHORIZED',
        ]);

        $this->expectException(WebhookSignatureException::class);

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testIsAuthorizedReturnsFalseForDeclined(): void
    {
        $payload = json_encode([
            'orderId' => 'ORD-001',
            'operationId' => 'OP-999',
            'operationType' => 'AUTHORIZATION',
            'operationResult' => 'DECLINED',
            'securityToken' => self::TOKEN,
        ]);

        $notification = $this->handler->handle($payload, self::TOKEN);

        $this->assertFalse($notification->isAuthorized());
    }

    public function testHandleTokenComparisonIsCaseSensitive(): void
    {
        $payload = json_encode([
            'orderId' => 'ORD-001',
            'securityToken' => strtoupper(self::TOKEN),
        ]);

        $this->expectException(WebhookSignatureException::class);

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testHandleWithEmptyTokenThrows(): void
    {
        $payload = json_encode([
            'orderId' => 'ORD-001',
            'securityToken' => '',
        ]);

        $this->expectException(WebhookSignatureException::class);

        $this->handler->handle($payload, self::TOKEN);
    }

    public function testRawDataIsPreservedOnNotification(): void
    {
        $data = [
            'orderId' => 'ORD-001',
            'operationId' => 'OP-999',
            'operationType' => 'AUTHORIZATION',
            'operationResult' => 'AUTHORIZED',
            'securityToken' => self::TOKEN,
            'customField' => 'extra-data',
        ];

        $notification = $this->handler->handle(json_encode($data), self::TOKEN);

        $this->assertSame('extra-data', $notification->getRaw()['customField']);
    }
}
