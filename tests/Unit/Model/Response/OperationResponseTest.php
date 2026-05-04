<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\OperationResponse;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers OperationResponse
 */
class OperationResponseTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'operationId' => 'OP-999',
            'operationTime' => '2024-01-01T12:00:00.001Z',
        ];

        $response = OperationResponse::fromArray($data);

        $this->assertSame('OP-999', $response->getOperationId());
        $this->assertSame('2024-01-01T12:00:00.001Z', $response->getOperationTime());
        $this->assertSame($data, $response->getRaw());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $response = OperationResponse::fromArray([]);

        $this->assertNull($response->getOperationId());
        $this->assertNull($response->getOperationTime());
    }

    public function testGetRawPreservesAllFields(): void
    {
        $data = ['operationId' => 'OP-1', 'operationTime' => '2024-01-01T12:00:00.001Z', 'extra' => 'value'];
        $response = OperationResponse::fromArray($data);

        $this->assertSame($data, $response->getRaw());
    }
}
