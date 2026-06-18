<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class OperationDetails implements ResponseModelInterface
{
    const CHANNEL_ECOMMERCE = 'ECOMMERCE';
    const CHANNEL_POS = 'POS';
    const CHANNEL_BACKOFFICE = 'BACKOFFICE';

    const OPERATION_TYPE_AUTHORIZATION = 'AUTHORIZATION';
    const OPERATION_TYPE_CAPTURE = 'CAPTURE';
    const OPERATION_TYPE_VOID = 'VOID';
    const OPERATION_TYPE_REFUND = 'REFUND';
    const OPERATION_TYPE_CANCEL = 'CANCEL';

    /** @var string|null */
    private $orderId;

    /** @var string|null */
    private $operationId;

    /** @var string|null */
    private $channel;

    /** @var string|null */
    private $channelDetail;

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
    private $paymentInstrumentInfo;

    /** @var string|null */
    private $paymentEndToEndId;

    /** @var string|null */
    private $cancelledOperationId;

    /** @var string|null */
    private $operationAmount;

    /** @var string|null */
    private $operationCurrency;

    /** @var string|null */
    private $paymentLinkId;

    /** @var string|null */
    private $terminalId;

    /** @var array<int, mixed> */
    private $warnings;

    /**
     * @param array<int, mixed> $warnings
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
        array $warnings
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
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['orderId'] ?? null,
            $data['operationId'] ?? null,
            $data['channel'] ?? null,
            $data['channelDetail'] ?? null,
            $data['operationType'] ?? null,
            $data['operationResult'] ?? null,
            $data['operationTime'] ?? null,
            $data['paymentMethod'] ?? null,
            $data['paymentCircuit'] ?? null,
            $data['paymentInstrumentInfo'] ?? null,
            $data['paymentEndToEndId'] ?? null,
            $data['cancelledOperationId'] ?? null,
            $data['operationAmount'] ?? null,
            $data['operationCurrency'] ?? null,
            $data['paymentLinkId'] ?? null,
            $data['terminalId'] ?? null,
            isset($data['warnings']) && is_array($data['warnings']) ? $data['warnings'] : []
        );
    }
}
