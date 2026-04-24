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
            'operationType' => 'REFUND',
            'operationResult' => 'REFUNDED',
        ];

        $response = OperationResponse::fromArray($data);

        $this->assertSame('OP-999', $response->getOperationId());
        $this->assertSame('REFUND', $response->getOperationType());
        $this->assertSame('REFUNDED', $response->getOperationResult());
        $this->assertSame($data, $response->getRaw());
    }

    /**
     * @dataProvider successfulResultsProvider
     */
    public function testIsSuccessfulReturnsTrueForSuccessfulResults(string $result): void
    {
        $response = OperationResponse::fromArray([
            'operationId' => 'OP-1',
            'operationType' => 'CAPTURE',
            'operationResult' => $result,
        ]);

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @return array<string, array{string}>
     */
    public function successfulResultsProvider(): array
    {
        return [
            'authorized' => [OperationResponse::RESULT_AUTHORIZED],
            'executed' => [OperationResponse::RESULT_EXECUTED],
            'refunded' => [OperationResponse::RESULT_REFUNDED],
            'voided' => [OperationResponse::RESULT_VOIDED],
        ];
    }

    public function testIsSuccessfulReturnsFalseForDeclined(): void
    {
        $response = OperationResponse::fromArray([
            'operationId' => 'OP-1',
            'operationType' => 'CAPTURE',
            'operationResult' => OperationResponse::RESULT_DECLINED,
        ]);

        $this->assertFalse($response->isSuccessful());
    }

    public function testIsSuccessfulReturnsFalseForCanceled(): void
    {
        $response = OperationResponse::fromArray([
            'operationId' => 'OP-1',
            'operationType' => 'CAPTURE',
            'operationResult' => OperationResponse::RESULT_CANCELED,
        ]);

        $this->assertFalse($response->isSuccessful());
    }

    public function testFromArrayWithMissingFieldsFallsBackToEmptyString(): void
    {
        $response = OperationResponse::fromArray([]);

        $this->assertSame('', $response->getOperationId());
        $this->assertSame('', $response->getOperationType());
        $this->assertSame('', $response->getOperationResult());
        $this->assertFalse($response->isSuccessful());
    }

    public function testGetRawPreservesAllFields(): void
    {
        $data = ['operationId' => 'OP-1', 'operationType' => 'REFUND', 'operationResult' => 'REFUNDED', 'extra' => 'value'];
        $response = OperationResponse::fromArray($data);

        $this->assertSame($data, $response->getRaw());
    }
}
