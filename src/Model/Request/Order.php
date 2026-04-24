<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class Order implements RequestModelInterface
{
    /** @var string */
    private $orderId;

    /** @var string */
    private $amount;

    /** @var string */
    private $currency;

    /** @var string|null */
    private $customerId;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $customField;

    /** @var CustomerInfo|null */
    private $customerInfo;

    public function __construct(
        string $orderId,
        string $amount,
        string $currency,
        ?string $customerId = null,
        ?string $description = null,
        ?string $customField = null,
        ?CustomerInfo $customerInfo = null
    ) {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerId = $customerId;
        $this->description = $description;
        $this->customField = $customField;
        $this->customerInfo = $customerInfo;
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

        return $data;
    }
}
