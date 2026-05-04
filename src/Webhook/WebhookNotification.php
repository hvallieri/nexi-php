<?php declare(strict_types=1);

namespace Hval\Nexi\Webhook;

class WebhookNotification
{
    /** @var string|null */
    private $eventId;

    /** @var string|null */
    private $eventTime;

    /** @var string|null */
    private $securityToken;

    /** @var string|null */
    private $orderId;

    /** @var string|null */
    private $operationId;

    /** @var string|null */
    private $channel;

    /** @var string|null */
    private $operationType;

    /** @var string|null */
    private $operationResult;

    /** @var string|null */
    private $operationTime;

    /** @var string|null */
    private $paymentMethod;

    /** @var string|null */
    private $paymentCircuit;

    /** @var string|null */
    private $operationAmount;

    /** @var string|null */
    private $operationCurrency;

    /** @var array<string, mixed> */
    private $raw;

    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        ?string $eventId,
        ?string $eventTime,
        ?string $securityToken,
        ?string $orderId,
        ?string $operationId,
        ?string $channel,
        ?string $operationType,
        ?string $operationResult,
        ?string $operationTime,
        ?string $paymentMethod,
        ?string $paymentCircuit,
        ?string $operationAmount,
        ?string $operationCurrency,
        array $raw
    ) {
        $this->eventId = $eventId;
        $this->eventTime = $eventTime;
        $this->securityToken = $securityToken;
        $this->orderId = $orderId;
        $this->operationId = $operationId;
        $this->channel = $channel;
        $this->operationType = $operationType;
        $this->operationResult = $operationResult;
        $this->operationTime = $operationTime;
        $this->paymentMethod = $paymentMethod;
        $this->paymentCircuit = $paymentCircuit;
        $this->operationAmount = $operationAmount;
        $this->operationCurrency = $operationCurrency;
        $this->raw = $raw;
    }

    public function getEventId(): ?string
    {
        return $this->eventId;
    }

    public function getEventTime(): ?string
    {
        return $this->eventTime;
    }

    public function getSecurityToken(): ?string
    {
        return $this->securityToken;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function getOperationResult(): ?string
    {
        return $this->operationResult;
    }

    public function getOperationTime(): ?string
    {
        return $this->operationTime;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getPaymentCircuit(): ?string
    {
        return $this->paymentCircuit;
    }

    public function getOperationAmount(): ?string
    {
        return $this->operationAmount;
    }

    public function getOperationCurrency(): ?string
    {
        return $this->operationCurrency;
    }

    public function isAuthorized(): bool
    {
        return $this->operationResult === 'AUTHORIZED';
    }

    public function isExecuted(): bool
    {
        return $this->operationResult === 'EXECUTED';
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
        $operation = isset($data['operation']) && is_array($data['operation']) ? $data['operation'] : [];

        return new self(
            $data['eventId'] ?? null,
            $data['eventTime'] ?? null,
            $data['securityToken'] ?? null,
            $operation['orderId'] ?? null,
            $operation['operationId'] ?? null,
            $operation['channel'] ?? null,
            $operation['operationType'] ?? null,
            $operation['operationResult'] ?? null,
            $operation['operationTime'] ?? null,
            $operation['paymentMethod'] ?? null,
            $operation['paymentCircuit'] ?? null,
            $operation['operationAmount'] ?? null,
            $operation['operationCurrency'] ?? null,
            $data
        );
    }
}
