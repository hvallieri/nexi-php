<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\OrderResponse;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers OrderResponse
 */
class OrderResponseTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'orderId' => 'ORD-001',
            'orderStatus' => 'AUTHORIZED',
            'amount' => '1000',
        ];

        $response = OrderResponse::fromArray($data);

        $this->assertSame('ORD-001', $response->getOrderId());
        $this->assertSame('AUTHORIZED', $response->getStatus());
        $this->assertSame($data, $response->getRaw());
    }

    public function testIsAuthorizedWithAuthorizedStatus(): void
    {
        $response = OrderResponse::fromArray(['orderId' => 'X', 'orderStatus' => 'AUTHORIZED']);

        $this->assertTrue($response->isAuthorized());
    }

    public function testIsAuthorizedWithExecutedStatus(): void
    {
        $response = OrderResponse::fromArray(['orderId' => 'X', 'orderStatus' => 'EXECUTED']);

        $this->assertTrue($response->isAuthorized());
    }

    public function testIsAuthorizedWithDeclinedStatus(): void
    {
        $response = OrderResponse::fromArray(['orderId' => 'X', 'orderStatus' => 'DECLINED']);

        $this->assertFalse($response->isAuthorized());
    }

    public function testIsAuthorizedWithPendingStatus(): void
    {
        $response = OrderResponse::fromArray(['orderId' => 'X', 'orderStatus' => 'PENDING']);

        $this->assertFalse($response->isAuthorized());
    }

    public function testIsAuthorizedWithCanceledStatus(): void
    {
        $response = OrderResponse::fromArray(['orderId' => 'X', 'orderStatus' => 'CANCELED']);

        $this->assertFalse($response->isAuthorized());
    }

    public function testFromArrayWithMissingFieldsFallsBackToEmptyString(): void
    {
        $response = OrderResponse::fromArray([]);

        $this->assertSame('', $response->getOrderId());
        $this->assertSame('', $response->getStatus());
        $this->assertFalse($response->isAuthorized());
    }

    public function testGetRawPreservesAllFields(): void
    {
        $data = ['orderId' => 'ORD-001', 'orderStatus' => 'AUTHORIZED', 'extra' => 'value'];
        $response = OrderResponse::fromArray($data);

        $this->assertSame($data, $response->getRaw());
    }
}
