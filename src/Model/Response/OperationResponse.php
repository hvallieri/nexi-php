<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class OperationResponse implements ResponseModelInterface
{
    const RESULT_AUTHORIZED = 'AUTHORIZED';
    const RESULT_EXECUTED = 'EXECUTED';
    const RESULT_DECLINED = 'DECLINED';
    const RESULT_CANCELED = 'CANCELED';
    const RESULT_VOIDED = 'VOIDED';
    const RESULT_REFUNDED = 'REFUNDED';

    /** @var string */
    private $operationId;

    /** @var string */
    private $operationType;

    /** @var string */
    private $operationResult;

    /** @var array<string, mixed> */
    private $raw;

    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        string $operationId,
        string $operationType,
        string $operationResult,
        array $raw
    ) {
        $this->operationId = $operationId;
        $this->operationType = $operationType;
        $this->operationResult = $operationResult;
        $this->raw = $raw;
    }

    public function getOperationId(): string
    {
        return $this->operationId;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getOperationResult(): string
    {
        return $this->operationResult;
    }

    public function isSuccessful(): bool
    {
        return $this->operationResult === self::RESULT_AUTHORIZED
            || $this->operationResult === self::RESULT_EXECUTED
            || $this->operationResult === self::RESULT_REFUNDED
            || $this->operationResult === self::RESULT_VOIDED;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['operationId'] ?? ''),
            (string) ($data['operationType'] ?? ''),
            (string) ($data['operationResult'] ?? ''),
            $data
        );
    }
}
