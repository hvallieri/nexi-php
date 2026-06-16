<?php declare(strict_types=1);

namespace Hval\Nexi\Webhook;

use Hval\Nexi\Model\Response\WebhookAdditionalData;

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

    /** @var string|null */
    private $paymentId;

    /** @var string|null */
    private $result;

    /** @var string|null */
    private $rootPaymentMethod;

    /** @var string|null */
    private $rootPaymentInstrumentInfo;

    /** @var string|null */
    private $orderAmount;

    /** @var string|null */
    private $currency;

    /** @var string|null */
    private $customerId;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $customField;

    /** @var string|null */
    private $orderTime;

    /** @var string|null */
    private $eventType;

    /** @var string|null */
    private $errorCode;

    /** @var string|null */
    private $errorMessage;

    /** @var WebhookAdditionalData|null */
    private $additionalData;

    /** @var string|null */
    private $paymentInstrumentInfo;

    /** @var string|null */
    private $paymentEndToEndId;

    /** @var string|null */
    private $cancelledOperationId;

    /** @var array<int, mixed> */
    private $warnings = [];

    /** @var string|null */
    private $paymentLinkId;

    /** @var string|null */
    private $terminalId;

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
     */
    public static function fromArray(array $data): self
    {
        $operation = isset($data['operation']) && is_array($data['operation']) ? $data['operation'] : [];
        $rawAdditionalData = isset($data['additionalData']) && is_array($data['additionalData']) ? $data['additionalData'] : null;

        $instance = new self(
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
