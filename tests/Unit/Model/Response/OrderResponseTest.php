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
    /** @return array<string, mixed> */
    private function makeData(string $orderId, string $operationResult): array
    {
        return [
            'orderStatus' => [
                'order' => [
                    'orderId' => $orderId,
                    'authorizedAmount' => '1000',
                    'capturedAmount' => '0',
                    'lastOperationType' => 'AUTHORIZATION',
                    'lastOperationTime' => '2024-01-01T12:00:00.000Z',
                    'operations' => [
                        [
                            'operationId' => 'OP-001',
                            'operationType' => 'AUTHORIZATION',
                            'operationResult' => $operationResult,
                            'operationTime' => '2024-01-01T12:00:00.000Z',
                        ],
                    ],
                ],
                'warnings' => [],
            ],
        ];
    }

    public function testFromArray(): void
    {
        $data = $this->makeData('ORD-001', 'AUTHORIZED');

        $response = OrderResponse::fromArray($data);

        $this->assertSame('ORD-001', $response->getOrderId());
        $this->assertSame('AUTHORIZED', $response->getLastOperationResult());
        $this->assertSame('1000', $response->getAuthorizedAmount());
        $this->assertSame('0', $response->getCapturedAmount());
        $this->assertSame('AUTHORIZATION', $response->getLastOperationType());
        $this->assertSame('2024-01-01T12:00:00.000Z', $response->getLastOperationTime());
        $this->assertCount(1, $response->getOperations());
        $this->assertSame($data, $response->getRaw());
    }

    public function testIsAuthorizedWithAuthorizedOperationResult(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'AUTHORIZED'));

        $this->assertTrue($response->isAuthorized());
    }

    public function testIsAuthorizedReturnsFalseForExecuted(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'EXECUTED'));

        $this->assertFalse($response->isAuthorized());
    }

    public function testIsExecutedWithExecutedOperationResult(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'EXECUTED'));

        $this->assertTrue($response->isExecuted());
    }

    public function testIsExecutedReturnsFalseForAuthorized(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'AUTHORIZED'));

        $this->assertFalse($response->isExecuted());
    }

    public function testIsAuthorizedWithDeclinedOperationResult(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'DECLINED'));

        $this->assertFalse($response->isAuthorized());
    }

    public function testIsAuthorizedWithPendingOperationResult(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'PENDING'));

        $this->assertFalse($response->isAuthorized());
    }

    public function testIsAuthorizedWithCanceledOperationResult(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'CANCELED'));

        $this->assertFalse($response->isAuthorized());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $response = OrderResponse::fromArray([]);

        $this->assertNull($response->getOrderId());
        $this->assertNull($response->getLastOperationResult());
        $this->assertNull($response->getAuthorizedAmount());
        $this->assertNull($response->getCapturedAmount());
        $this->assertNull($response->getLastOperationType());
        $this->assertNull($response->getLastOperationTime());
        $this->assertSame([], $response->getOperations());
        $this->assertFalse($response->isAuthorized());
    }

    public function testLastOperationResultIsNullWhenOperationsIsEmpty(): void
    {
        $data = [
            'orderStatus' => [
                'order' => [
                    'orderId' => 'ORD-001',
                    'operations' => [],
                ],
            ],
        ];

        $response = OrderResponse::fromArray($data);

        $this->assertNull($response->getLastOperationResult());
        $this->assertSame([], $response->getOperations());
    }

    public function testIsExecutedReturnsFalseForDeclined(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'DECLINED'));

        $this->assertFalse($response->isExecuted());
    }

    public function testIsExecutedReturnsFalseForCanceled(): void
    {
        $response = OrderResponse::fromArray($this->makeData('X', 'CANCELED'));

        $this->assertFalse($response->isExecuted());
    }

    public function testFirstOperationDeterminesLastOperationResult(): void
    {
        $data = [
            'orderStatus' => [
                'order' => [
                    'orderId' => 'ORD-001',
                    'operations' => [
                        ['operationResult' => 'AUTHORIZED'],
                        ['operationResult' => 'PENDING'],
                    ],
                ],
            ],
        ];

        $response = OrderResponse::fromArray($data);

        $this->assertSame('AUTHORIZED', $response->getLastOperationResult());
        $this->assertCount(2, $response->getOperations());
    }

    public function testOrderStatusPresentButOrderKeyMissingReturnsNulls(): void
    {
        $response = OrderResponse::fromArray(['orderStatus' => ['warnings' => []]]);

        $this->assertNull($response->getOrderId());
        $this->assertNull($response->getLastOperationResult());
        $this->assertSame([], $response->getOperations());
    }

    public function testGetRawPreservesAllFields(): void
    {
        $data = $this->makeData('ORD-001', 'AUTHORIZED');
        $response = OrderResponse::fromArray($data);

        $this->assertSame($data, $response->getRaw());
    }
}
