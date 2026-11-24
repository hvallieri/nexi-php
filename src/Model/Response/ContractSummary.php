<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class ContractSummary implements ResponseModelInterface
{
    private ?string $contractId;
    private ?string $contractType;
    private ?string $contractExpiryDate;
    private ?string $contractFrequency;
    private ?string $paymentMethod;
    private ?string $paymentCircuit;
    private ?string $paymentInstrumentInfo;

    public function __construct(
        ?string $contractId,
        ?string $contractType,
        ?string $contractExpiryDate,
        ?string $contractFrequency,
        ?string $paymentMethod,
        ?string $paymentCircuit,
        ?string $paymentInstrumentInfo,
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
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            contractId: $data['contractId'] ?? null,
            contractType: $data['contractType'] ?? null,
            contractExpiryDate: $data['contractExpiryDate'] ?? null,
            contractFrequency: $data['contractFrequency'] ?? null,
            paymentMethod: $data['paymentMethod'] ?? null,
            paymentCircuit: $data['paymentCircuit'] ?? null,
            paymentInstrumentInfo: $data['paymentInstrumentInfo'] ?? null,
        );
    }
}
