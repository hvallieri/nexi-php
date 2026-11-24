<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class CustomerInfo implements RequestModelInterface
{
    private ?string $cardHolderName;
    private ?string $cardHolderEmail;
    private ?Address $billingAddress;
    private ?Address $shippingAddress;
    private ?string $mobilePhoneCountryCode;
    private ?string $mobilePhone;
    private ?string $homePhone;
    private ?string $workPhone;

    public function __construct(
        ?string $cardHolderName = null,
        ?string $cardHolderEmail = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
        ?string $mobilePhoneCountryCode = null,
        ?string $mobilePhone = null,
        ?string $homePhone = null,
        ?string $workPhone = null,
    ) {
        $this->cardHolderName = $cardHolderName;
        $this->cardHolderEmail = $cardHolderEmail;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->mobilePhoneCountryCode = $mobilePhoneCountryCode;
        $this->mobilePhone = $mobilePhone;
        $this->homePhone = $homePhone;
        $this->workPhone = $workPhone;
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

        if ($this->mobilePhoneCountryCode !== null) {
            $data['mobilePhoneCountryCode'] = $this->mobilePhoneCountryCode;
        }

        if ($this->mobilePhone !== null) {
            $data['mobilePhone'] = $this->mobilePhone;
        }

        if ($this->homePhone !== null) {
            $data['homePhone'] = $this->homePhone;
        }

        if ($this->workPhone !== null) {
            $data['workPhone'] = $this->workPhone;
        }

        return $data;
    }
}
