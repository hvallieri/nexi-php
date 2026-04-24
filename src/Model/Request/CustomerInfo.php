<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class CustomerInfo implements RequestModelInterface
{
    /** @var string|null */
    private $cardHolderName;

    /** @var string|null */
    private $cardHolderEmail;

    /** @var Address|null */
    private $billingAddress;

    /** @var Address|null */
    private $shippingAddress;

    public function __construct(
        ?string $cardHolderName = null,
        ?string $cardHolderEmail = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null
    ) {
        $this->cardHolderName = $cardHolderName;
        $this->cardHolderEmail = $cardHolderEmail;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->cardHolderName !== null) {
            $data['cardHolderName'] = $this->cardHolderName;
        }

        if ($this->cardHolderEmail !== null) {
            $data['cardHolderEmail'] = $this->cardHolderEmail;
        }

        if ($this->billingAddress !== null) {
            $data['billingAddress'] = $this->billingAddress->toArray();
        }

        if ($this->shippingAddress !== null) {
            $data['shippingAddress'] = $this->shippingAddress->toArray();
        }

        return $data;
    }
}
