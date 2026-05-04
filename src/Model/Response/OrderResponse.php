<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class OrderResponse implements ResponseModelInterface
{
    const OPERATION_RESULT_PENDING = 'PENDING';
    const OPERATION_RESULT_AUTHORIZED = 'AUTHORIZED';
    const OPERATION_RESULT_EXECUTED = 'EXECUTED';
    const OPERATION_RESULT_DECLINED = 'DECLINED';
    const OPERATION_RESULT_DENIED_BY_RISK = 'DENIED_BY_RISK';
    const OPERATION_RESULT_THREEDS_VALIDATED = 'THREEDS_VALIDATED';
    const OPERATION_RESULT_THREEDS_FAILED = 'THREEDS_FAILED';
    const OPERATION_RESULT_CANCELED = 'CANCELED';
    const OPERATION_RESULT_VOIDED = 'VOIDED';
    const OPERATION_RESULT_REFUNDED = 'REFUNDED';
    const OPERATION_RESULT_FAILED = 'FAILED';

    /** @var string|null */
    private $orderId;

    /** @var string|null */
    private $lastOperationResult;

    /** @var string|null */
    private $authorizedAmount;

    /** @var string|null */
    private $capturedAmount;

    /** @var string|null */
    private $lastOperationType;

    /** @var string|null */
    private $lastOperationTime;

    /** @var array<int, array<string, mixed>> */
    private $operations;

    /** @var array<string, mixed> */
    private $raw;

    /**
     * @param array<int, array<string, mixed>> $operations
     * @param array<string, mixed> $raw
     */
    public function __construct(
        ?string $orderId,
        ?string $lastOperationResult,
        ?string $authorizedAmount,
        ?string $capturedAmount,
        ?string $lastOperationType,
        ?string $lastOperationTime,
        array $operations,
        array $raw
    ) {
        $this->orderId = $orderId;
        $this->lastOperationResult = $lastOperationResult;
        $this->authorizedAmount = $authorizedAmount;
        $this->capturedAmount = $capturedAmount;
        $this->lastOperationType = $lastOperationType;
        $this->lastOperationTime = $lastOperationTime;
        $this->operations = $operations;
        $this->raw = $raw;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getLastOperationResult(): ?string
    {
        return $this->lastOperationResult;
    }

    public function getAuthorizedAmount(): ?string
    {
        return $this->authorizedAmount;
    }

    public function getCapturedAmount(): ?string
    {
        return $this->capturedAmount;
    }

    public function getLastOperationType(): ?string
    {
        return $this->lastOperationType;
    }

    public function getLastOperationTime(): ?string
    {
        return $this->lastOperationTime;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function isAuthorized(): bool
    {
        return $this->lastOperationResult === self::OPERATION_RESULT_AUTHORIZED;
    }

    public function isExecuted(): bool
    {
        return $this->lastOperationResult === self::OPERATION_RESULT_EXECUTED;
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
        $orderStatus = isset($data['orderStatus']) && is_array($data['orderStatus']) ? $data['orderStatus'] : [];
        $order = isset($orderStatus['order']) && is_array($orderStatus['order']) ? $orderStatus['order'] : [];
        $operations = isset($order['operations']) && is_array($order['operations']) ? $order['operations'] : [];
        $lastOperation = isset($operations[0]) && is_array($operations[0]) ? $operations[0] : [];

        return new self(
            $order['orderId'] ?? null,
            $lastOperation['operationResult'] ?? null,
            $order['authorizedAmount'] ?? null,
            $order['capturedAmount'] ?? null,
            $order['lastOperationType'] ?? null,
            $order['lastOperationTime'] ?? null,
            $operations,
            $data
        );
    }
}
