<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class ContractSummary implements ResponseModelInterface
{
    /** @var string|null */
    private $contractId;

    /** @var string|null */
    private $contractType;

    /** @var string|null */
    private $contractExpiryDate;

    /** @var string|null */
    private $contractFrequency;

    /** @var string|null */
    private $paymentMethod;

    /** @var string|null */
    private $paymentCircuit;

    /** @var string|null */
    private $paymentInstrumentInfo;

    public function __construct(
        ?string $contractId,
        ?string $contractType,
        ?string $contractExpiryDate,
        ?string $contractFrequency,
        ?string $paymentMethod,
        ?string $paymentCircuit,
        ?string $paymentInstrumentInfo
    ) {
        $this->contractId = $contractId;
        $this->contractType = $contractType;
        $this->contractExpiryDate = $contractExpiryDate;
        $this->contractFrequency = $contractFrequency;
        $this->paymentMethod = $paymentMethod;
        $this->paymentCircuit = $paymentCircuit;
        $this->paymentInstrumentInfo = $paymentInstrumentInfo;
    }

    public function getContractId(): ?string
    {
        return $this->contractId;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function getContractExpiryDate(): ?string
    {
        return $this->contractExpiryDate;
    }

    public function getContractFrequency(): ?string
    {
        return $this->contractFrequency;
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

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['contractId'] ?? null,
            $data['contractType'] ?? null,
            $data['contractExpiryDate'] ?? null,
            $data['contractFrequency'] ?? null,
            $data['paymentMethod'] ?? null,
            $data['paymentCircuit'] ?? null,
            $data['paymentInstrumentInfo'] ?? null
        );
    }
}
