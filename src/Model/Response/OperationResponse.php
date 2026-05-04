<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class OperationResponse implements ResponseModelInterface
{
    /** @var string|null */
    private $operationId;

    /** @var string|null */
    private $operationTime;

    /** @var array<string, mixed> */
    private $raw;

    /**
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
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['operationId'] ?? null,
            $data['operationTime'] ?? null,
            $data
        );
    }
}
