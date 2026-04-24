<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class Address implements RequestModelInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var string */
    private $postCode;

    /** @var string */
    private $country;

    /** @var string|null */
    private $province;

    /** @var string|null */
    private $additionalInfo;

    public function __construct(
        string $name,
        string $street,
        string $city,
        string $postCode,
        string $country,
        ?string $province = null,
        ?string $additionalInfo = null
    ) {
        $this->name = $name;
        $this->street = $street;
        $this->city = $city;
        $this->postCode = $postCode;
        $this->country = $country;
        $this->province = $province;
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'street' => $this->street,
            'city' => $this->city,
            'postCode' => $this->postCode,
            'country' => $this->country,
        ];

        if ($this->province !== null) {
            $data['province'] = $this->province;
        }

        if ($this->additionalInfo !== null) {
            $data['additionalInfo'] = $this->additionalInfo;
        }

        return $data;
    }
}
