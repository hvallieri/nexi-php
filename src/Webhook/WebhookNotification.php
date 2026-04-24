<?php declare(strict_types=1);

namespace Hval\Nexi\Webhook;

class WebhookNotification
{
    /** @var string */
    private $orderId;

    /** @var string */
    private $operationId;

    /** @var string */
    private $operationType;

    /** @var string */
    private $operationResult;

    /** @var string */
    private $securityToken;

    /** @var array<string, mixed> */
    private $raw;

    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        string $orderId,
        string $operationId,
        string $operationType,
        string $operationResult,
        string $securityToken,
        array $raw
    ) {
        $this->orderId = $orderId;
        $this->operationId = $operationId;
        $this->operationType = $operationType;
        $this->operationResult = $operationResult;
        $this->securityToken = $securityToken;
        $this->raw = $raw;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
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

    public function getSecurityToken(): string
    {
        return $this->securityToken;
    }

    public function isAuthorized(): bool
    {
        return $this->operationResult === 'AUTHORIZED';
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
            (string) ($data['orderId'] ?? ''),
            (string) ($data['operationId'] ?? ''),
            (string) ($data['operationType'] ?? ''),
            (string) ($data['operationResult'] ?? ''),
            (string) ($data['securityToken'] ?? ''),
            $data
        );
    }
}
