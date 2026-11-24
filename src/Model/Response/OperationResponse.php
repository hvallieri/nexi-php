<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

final class OperationResponse implements ResponseModelInterface
{
    private ?string $operationId;
    private ?string $operationTime;
    /** @var array<string, mixed> */
    private array $raw;

    /**
     * @param string|null $operationId
     * @param string|null $operationTime
     * @param array<string, mixed> $raw
     */
    public function __construct(?string $operationId, ?string $operationTime, array $raw)
    {
        $this->operationId = $operationId;
        $this->operationTime = $operationTime;
        $this->raw = $raw;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function getOperationTime(): ?string
    {
        return $this->operationTime;
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
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            operationId: $data['operationId'] ?? null,
            operationTime: $data['operationTime'] ?? null,
            raw: $data,
        );
    }
}
