<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\WebhookAdditionalData;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers WebhookAdditionalData
 */
class WebhookAdditionalDataTest extends TestCase
{
    public function testFromArrayPopulatesAllFields(): void
    {
        $data = [
            'status' => 'AUTHORIZED',
            'authorizationCode' => 'AUTH123',
            'rrn' => 'RRN456',
            'maskedPan' => '************1234',
            'cardExpiryDate' => '12/26',
            'cardType' => 'CREDIT',
            'cardCountry' => 'IT',
            'threeDS' => 'FULL_SECURE',
            'schemaTID' => 'TID789',
            'amountInMerchantCurrency' => '950',
            'merchantCurrency' => 'GBP',
            'exchangeRate' => '1.05',
        ];

        $additional = WebhookAdditionalData::fromArray($data);

        $this->assertSame('AUTHORIZED', $additional->getStatus());
        $this->assertSame('AUTH123', $additional->getAuthorizationCode());
        $this->assertSame('RRN456', $additional->getRrn());
        $this->assertSame('************1234', $additional->getMaskedPan());
        $this->assertSame('12/26', $additional->getCardExpiryDate());
        $this->assertSame('CREDIT', $additional->getCardType());
        $this->assertSame('IT', $additional->getCardCountry());
        $this->assertSame('FULL_SECURE', $additional->getThreeDS());
        $this->assertSame('TID789', $additional->getSchemaTID());
        $this->assertSame('950', $additional->getAmountInMerchantCurrency());
        $this->assertSame('GBP', $additional->getMerchantCurrency());
        $this->assertSame('1.05', $additional->getExchangeRate());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $additional = WebhookAdditionalData::fromArray([]);

        $this->assertNull($additional->getStatus());
        $this->assertNull($additional->getAuthorizationCode());
        $this->assertNull($additional->getRrn());
        $this->assertNull($additional->getMaskedPan());
        $this->assertNull($additional->getCardExpiryDate());
        $this->assertNull($additional->getCardType());
        $this->assertNull($additional->getCardCountry());
        $this->assertNull($additional->getThreeDS());
        $this->assertNull($additional->getSchemaTID());
        $this->assertNull($additional->getAmountInMerchantCurrency());
        $this->assertNull($additional->getMerchantCurrency());
        $this->assertNull($additional->getExchangeRate());
    }
}
