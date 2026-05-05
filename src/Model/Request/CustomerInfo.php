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

    /** @var string|null */
    private $mobilePhoneCountryCode;

    /** @var string|null */
    private $mobilePhone;

    /** @var string|null */
    private $homePhone;

    /** @var string|null */
    private $workPhone;

    public function __construct(
        ?string $cardHolderName = null,
        ?string $cardHolderEmail = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
        ?string $mobilePhoneCountryCode = null,
        ?string $mobilePhone = null,
        ?string $homePhone = null,
        ?string $workPhone = null
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
