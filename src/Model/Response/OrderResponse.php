<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Enum\OperationResult;
use Hval\Nexi\Model\ResponseModelInterface;

final class OrderResponse implements ResponseModelInterface
{
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
        array $raw,
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
        return $this->lastOperationResult === OperationResult::Authorized->value;
    }

    public function isExecuted(): bool
    {
        return $this->lastOperationResult === OperationResult::Executed->value;
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
        $orderStatus = isset($data['orderStatus']) && is_array($data['orderStatus']) ? $data['orderStatus'] : [];
        $order = isset($orderStatus['order']) && is_array($orderStatus['order']) ? $orderStatus['order'] : [];
        $operations = isset($data['operations']) && is_array($data['operations']) ? $data['operations'] : [];
        $lastOperation = end($operations);
        $lastOperation = is_array($lastOperation) === true ? $lastOperation : [];

        return new self(
            $order['orderId'] ?? null,
            $lastOperation['operationResult'] ?? null,
            $orderStatus['authorizedAmount'] ?? null,
            $orderStatus['capturedAmount'] ?? null,
            $orderStatus['lastOperationType'] ?? null,
            $orderStatus['lastOperationTime'] ?? null,
            $operations,
            $data,
        );
    }
}
