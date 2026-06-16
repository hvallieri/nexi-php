<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class WebhookAdditionalData implements ResponseModelInterface
{
    /** @var string|null */
    private $status;

    /** @var string|null */
    private $authorizationCode;

    /** @var string|null */
    private $rrn;

    /** @var string|null */
    private $maskedPan;

    /** @var string|null */
    private $cardExpiryDate;

    /** @var string|null */
    private $cardType;

    /** @var string|null */
    private $cardCountry;

    /** @var string|null */
    private $threeDS;

    /** @var string|null */
    private $schemaTID;

    /** @var string|null */
    private $amountInMerchantCurrency;

    /** @var string|null */
    private $merchantCurrency;

    /** @var string|null */
    private $exchangeRate;

    private function __construct()
    {
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function getRrn(): ?string
    {
        return $this->rrn;
    }

    public function getMaskedPan(): ?string
    {
        return $this->maskedPan;
    }

    public function getCardExpiryDate(): ?string
    {
        return $this->cardExpiryDate;
    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function getCardCountry(): ?string
    {
        return $this->cardCountry;
    }

    public function getThreeDS(): ?string
    {
        return $this->threeDS;
    }

    public function getSchemaTID(): ?string
    {
        return $this->schemaTID;
    }

    public function getAmountInMerchantCurrency(): ?string
    {
        return $this->amountInMerchantCurrency;
    }

    public function getMerchantCurrency(): ?string
    {
        return $this->merchantCurrency;
    }

    public function getExchangeRate(): ?string
    {
        return $this->exchangeRate;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->status = $data['status'] ?? null;
        $instance->authorizationCode = $data['authorizationCode'] ?? null;
        $instance->rrn = $data['rrn'] ?? null;
        $instance->maskedPan = $data['maskedPan'] ?? null;
        $instance->cardExpiryDate = $data['cardExpiryDate'] ?? null;
        $instance->cardType = $data['cardType'] ?? null;
        $instance->cardCountry = $data['cardCountry'] ?? null;
        $instance->threeDS = $data['threeDS'] ?? null;
        $instance->schemaTID = $data['schemaTID'] ?? null;
        $instance->amountInMerchantCurrency = $data['amountInMerchantCurrency'] ?? null;
        $instance->merchantCurrency = $data['merchantCurrency'] ?? null;
        $instance->exchangeRate = $data['exchangeRate'] ?? null;

        return $instance;
    }
}
