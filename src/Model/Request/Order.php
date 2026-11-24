<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class Order implements RequestModelInterface
{
    private string $orderId;
    private string $amount;
    private string $currency;
    private ?string $customerId;
    private ?string $description;
    private ?string $customField;
    private ?CustomerInfo $customerInfo;
    /** @var array<int, string>|null */
    private ?array $termsAndConditionsIds;
    private ?string $transactionSummary;
    /** @var array<int, mixed>|null */
    private ?array $installments;
    private ?int $installmentQty;

    public function __construct(
        string $orderId,
        string $amount,
        string $currency,
        ?string $customerId = null,
        ?string $description = null,
        ?string $customField = null,
        ?CustomerInfo $customerInfo = null,
        ?array $termsAndConditionsIds = null,
        ?string $transactionSummary = null,
        ?array $installments = null,
        ?int $installmentQty = null,
    ) {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerId = $customerId;
        $this->description = $description;
        $this->customField = $customField;
        $this->customerInfo = $customerInfo;
        $this->termsAndConditionsIds = $termsAndConditionsIds;
        $this->transactionSummary = $transactionSummary;
        $this->installments = $installments;
        $this->installmentQty = $installmentQty;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'orderId' => $this->orderId,
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];

        if ($this->customerId !== null) {
            $data['customerId'] = $this->customerId;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->customField !== null) {
            $data['customField'] = $this->customField;
        }

        if ($this->customerInfo !== null) {
            $data['customerInfo'] = $this->customerInfo->toArray();
        }

        if ($this->termsAndConditionsIds !== null) {
            $data['termsAndConditionsIds'] = $this->termsAndConditionsIds;
        }

        if ($this->transactionSummary !== null) {
            $data['transactionSummary'] = $this->transactionSummary;
        }

        if ($this->installments !== null) {
            $data['installments'] = $this->installments;
        }

        if ($this->installmentQty !== null) {
            $data['installmentQty'] = $this->installmentQty;
        }

        return $data;
    }
}
