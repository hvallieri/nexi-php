<?php declare(strict_types=1);

namespace Hval\Nexi\Webhook;

use Hval\Nexi\Model\Response\WebhookAdditionalData;

class WebhookNotification
{
    private ?string $eventId;
    private ?string $eventTime;
    private ?string $securityToken;
    private ?string $orderId;
    private ?string $operationId;
    private ?string $channel;
    private ?string $operationType;
    private ?string $operationResult;
    private ?string $operationTime;
    private ?string $paymentMethod;
    private ?string $paymentCircuit;
    private ?string $operationAmount;
    private ?string $operationCurrency;
    /** @var array<string, mixed> */
    private array $raw;
    private ?string $paymentId;
    private ?string $result;
    private ?string $rootPaymentMethod;
    private ?string $rootPaymentInstrumentInfo;
    private ?string $orderAmount;
    private ?string $currency;
    private ?string $customerId;
    private ?string $description;
    private ?string $customField;
    private ?string $orderTime;
    private ?string $eventType;
    private ?string $errorCode;
    private ?string $errorMessage;
    private ?WebhookAdditionalData $additionalData;
    private ?string $paymentInstrumentInfo;
    private ?string $paymentEndToEndId;
    private ?string $cancelledOperationId;
    /** @var array<int, mixed> */
    private array $warnings = [];
    private ?string $paymentLinkId;
    private ?string $terminalId;

    /**
     * @param string|null $eventId
     * @param string|null $eventTime
     * @param string|null $securityToken
     * @param string|null $orderId
     * @param string|null $operationId
     * @param string|null $channel
     * @param string|null $operationType
     * @param string|null $operationResult
     * @param string|null $operationTime
     * @param string|null $paymentMethod
     * @param string|null $paymentCircuit
     * @param string|null $operationAmount
     * @param string|null $operationCurrency
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
        array $raw,
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

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function getRootPaymentMethod(): ?string
    {
        return $this->rootPaymentMethod;
    }

    public function getRootPaymentInstrumentInfo(): ?string
    {
        return $this->rootPaymentInstrumentInfo;
    }

    public function getOrderAmount(): ?string
    {
        return $this->orderAmount;
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

    public function getOrderTime(): ?string
    {
        return $this->orderTime;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getAdditionalData(): ?WebhookAdditionalData
    {
        return $this->additionalData;
    }

    public function getPaymentInstrumentInfo(): ?string
    {
        return $this->paymentInstrumentInfo;
    }

    public function getPaymentEndToEndId(): ?string
    {
        return $this->paymentEndToEndId;
    }

    public function getCancelledOperationId(): ?string
    {
        return $this->cancelledOperationId;
    }

    /**
     * @return array<int, mixed>
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getPaymentLinkId(): ?string
    {
        return $this->paymentLinkId;
    }

    public function getTerminalId(): ?string
    {
        return $this->terminalId;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $operation = isset($data['operation']) && is_array($data['operation']) ? $data['operation'] : [];
        $rawAdditionalData = isset($data['additionalData']) && is_array($data['additionalData']) ? $data['additionalData'] : null;

        $instance = new self(
            eventId: $data['eventId'] ?? null,
            eventTime: $data['eventTime'] ?? null,
            securityToken: $data['securityToken'] ?? null,
            orderId: $operation['orderId'] ?? null,
            operationId: $operation['operationId'] ?? null,
            channel: $operation['channel'] ?? null,
            operationType: $operation['operationType'] ?? null,
            operationResult: $operation['operationResult'] ?? null,
            operationTime: $operation['operationTime'] ?? null,
            paymentMethod: $operation['paymentMethod'] ?? null,
            paymentCircuit: $operation['paymentCircuit'] ?? null,
            operationAmount: $operation['operationAmount'] ?? null,
            operationCurrency: $operation['operationCurrency'] ?? null,
            raw: $data,
        );

        $instance->paymentId = $data['paymentId'] ?? null;
        $instance->result = $data['result'] ?? null;
        $instance->rootPaymentMethod = $data['paymentMethod'] ?? null;
        $instance->rootPaymentInstrumentInfo = $data['paymentInstrumentInfo'] ?? null;
        $instance->orderAmount = $data['orderAmount'] ?? null;
        $instance->currency = $data['currency'] ?? null;
        $instance->customerId = $data['customerId'] ?? null;
        $instance->description = $data['description'] ?? null;
        $instance->customField = $data['customField'] ?? null;
        $instance->orderTime = $data['orderTime'] ?? null;
        $instance->eventType = $data['eventType'] ?? null;
        $instance->errorCode = $data['errorCode'] ?? null;
        $instance->errorMessage = $data['errorMessage'] ?? null;
        $instance->additionalData = $rawAdditionalData !== null ? WebhookAdditionalData::fromArray($rawAdditionalData) : null;

        $instance->paymentInstrumentInfo = $operation['paymentInstrumentInfo'] ?? null;
        $instance->paymentEndToEndId = $operation['paymentEndToEndId'] ?? null;
        $instance->cancelledOperationId = $operation['cancelledOperationId'] ?? null;
        $instance->warnings = isset($operation['warnings']) && is_array($operation['warnings']) ? $operation['warnings'] : [];
        $instance->paymentLinkId = $operation['paymentLinkId'] ?? null;
        $instance->terminalId = $operation['terminalId'] ?? null;

        return $instance;
    }
}
