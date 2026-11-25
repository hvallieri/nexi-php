<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class OrderSummary implements ResponseModelInterface
{
    /** @var string|null */
    private $orderId;

    /** @var string|null */
    private $amount;

    /** @var string|null */
    private $currency;

    /** @var string|null */
    private $customerId;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $customField;

    /** @var string|null */
    private $authorizedAmount;

    /** @var string|null */
    private $capturedAmount;

    /** @var string|null */
    private $lastOperationType;

    /** @var string|null */
    private $lastOperationTime;

    /** @var array<int, mixed>|null */
    private $termsAndConditionsIds;

    public function __construct(
        ?string $orderId,
        ?string $amount,
        ?string $currency,
        ?string $customerId,
        ?string $description,
        ?string $customField,
        ?string $authorizedAmount,
        ?string $capturedAmount,
        ?string $lastOperationType,
        ?string $lastOperationTime,
        ?array $termsAndConditionsIds
    ) {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerId = $customerId;
        $this->description = $description;
        $this->customField = $customField;
        $this->authorizedAmount = $authorizedAmount;
        $this->capturedAmount = $capturedAmount;
        $this->lastOperationType = $lastOperationType;
        $this->lastOperationTime = $lastOperationTime;
        $this->termsAndConditionsIds = $termsAndConditionsIds;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCustomField(): ?string
    {
        return $this->customField;
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
     * @return array<int, mixed>|null
     */
    public function getTermsAndConditionsIds(): ?array
    {
        return $this->termsAndConditionsIds;
    }

    /**
     * Nexi nests the order fields under "order"; an item without it is read from its root.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $order = isset($data['order']) && is_array($data['order']) ? $data['order'] : $data;
        $amount = isset($order['amount']) ? (string) $order['amount'] : null;

        return new self(
            $order['orderId'] ?? null,
            $amount,
            $order['currency'] ?? null,
            $order['customerId'] ?? null,
            $order['description'] ?? null,
            $order['customField'] ?? null,
            $data['authorizedAmount'] ?? null,
            $data['capturedAmount'] ?? null,
            $data['lastOperationType'] ?? null,
            $data['lastOperationTime'] ?? null,
            isset($order['termsAndConditionsIds']) && is_array($order['termsAndConditionsIds']) ? $order['termsAndConditionsIds'] : null
        );
    }
}
