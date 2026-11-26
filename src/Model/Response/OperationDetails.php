<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

final class OperationDetails implements ResponseModelInterface
{
    private ?string $orderId;
    private ?string $operationId;
    private ?string $channel;
    private ?string $channelDetail;
    private ?string $operationType;
    private ?string $operationResult;
    private ?string $operationTime;
    private ?string $paymentMethod;
    private ?string $paymentCircuit;
    private ?string $paymentInstrumentInfo;
    private ?string $paymentEndToEndId;
    private ?string $cancelledOperationId;
    private ?string $operationAmount;
    private ?string $operationCurrency;
    private ?string $paymentLinkId;
    private ?string $terminalId;
    /** @var array<int, mixed> */
    private array $warnings;
    /** @var array<string, mixed> */
    private array $customerInfo;
    /** @var array<string, mixed> */
    private array $additionalData;

    /**
     * @param string|null $orderId
     * @param string|null $operationId
     * @param string|null $channel
     * @param string|null $channelDetail
     * @param string|null $operationType
     * @param string|null $operationResult
     * @param string|null $operationTime
     * @param string|null $paymentMethod
     * @param string|null $paymentCircuit
     * @param string|null $paymentInstrumentInfo
     * @param string|null $paymentEndToEndId
     * @param string|null $cancelledOperationId
     * @param string|null $operationAmount
     * @param string|null $operationCurrency
     * @param string|null $paymentLinkId
     * @param string|null $terminalId
     * @param array<int, mixed> $warnings
     * @param array<string, mixed> $customerInfo
     * @param array<string, mixed> $additionalData
     */
    public function __construct(
        ?string $orderId,
        ?string $operationId,
        ?string $channel,
        ?string $channelDetail,
        ?string $operationType,
        ?string $operationResult,
        ?string $operationTime,
        ?string $paymentMethod,
        ?string $paymentCircuit,
        ?string $paymentInstrumentInfo,
        ?string $paymentEndToEndId,
        ?string $cancelledOperationId,
        ?string $operationAmount,
        ?string $operationCurrency,
        ?string $paymentLinkId,
        ?string $terminalId,
        array $warnings,
        array $customerInfo = [],
        array $additionalData = [],
    ) {
        $this->orderId = $orderId;
        $this->operationId = $operationId;
        $this->channel = $channel;
        $this->channelDetail = $channelDetail;
        $this->operationType = $operationType;
        $this->operationResult = $operationResult;
        $this->operationTime = $operationTime;
        $this->paymentMethod = $paymentMethod;
        $this->paymentCircuit = $paymentCircuit;
        $this->paymentInstrumentInfo = $paymentInstrumentInfo;
        $this->paymentEndToEndId = $paymentEndToEndId;
        $this->cancelledOperationId = $cancelledOperationId;
        $this->operationAmount = $operationAmount;
        $this->operationCurrency = $operationCurrency;
        $this->paymentLinkId = $paymentLinkId;
        $this->terminalId = $terminalId;
        $this->warnings = $warnings;
        $this->customerInfo = $customerInfo;
        $this->additionalData = $additionalData;
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

    public function getChannelDetail(): ?string
    {
        return $this->channelDetail;
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

    public function getOperationAmount(): ?string
    {
        return $this->operationAmount;
    }

    public function getOperationCurrency(): ?string
    {
        return $this->operationCurrency;
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
     * @return array<int, mixed>
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomerInfo(): array
    {
        return $this->customerInfo;
    }

    /**
     * Map of additional fields specific to the payment method
     * (e.g. authorizationCode, cardCountry, threeDS).
     *
     * @return array<string, mixed>
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * Nexi sends terminalId inside additionalData (e.g. on MIT operations): it is read from there
     * when it is not at the root.
     *
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            orderId: $data['orderId'] ?? null,
            operationId: $data['operationId'] ?? null,
            channel: $data['channel'] ?? null,
            channelDetail: $data['channelDetail'] ?? null,
            operationType: $data['operationType'] ?? null,
            operationResult: $data['operationResult'] ?? null,
            operationTime: $data['operationTime'] ?? null,
            paymentMethod: $data['paymentMethod'] ?? null,
            paymentCircuit: $data['paymentCircuit'] ?? null,
            paymentInstrumentInfo: $data['paymentInstrumentInfo'] ?? null,
            paymentEndToEndId: $data['paymentEndToEndId'] ?? null,
            cancelledOperationId: $data['cancelledOperationId'] ?? null,
            operationAmount: $data['operationAmount'] ?? null,
            operationCurrency: $data['operationCurrency'] ?? null,
            paymentLinkId: $data['paymentLinkId'] ?? null,
            terminalId: $data['terminalId'] ?? $data['additionalData']['terminalId'] ?? null,
            warnings: isset($data['warnings']) && is_array($data['warnings']) ? $data['warnings'] : [],
            customerInfo: isset($data['customerInfo']) && is_array($data['customerInfo']) ? $data['customerInfo'] : [],
            additionalData: isset($data['additionalData']) && is_array($data['additionalData']) ? $data['additionalData'] : [],
        );
    }
}
