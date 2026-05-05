<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\Address;
use Hval\Nexi\Model\Request\CustomerInfo;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers CustomerInfo
 */
class CustomerInfoTest extends TestCase
{
    public function testToArrayWithNoFields(): void
    {
        $info = new CustomerInfo();

        $this->assertSame([], $info->toArray());
    }

    public function testToArrayWithAllFields(): void
    {
        $billing = new Address('Mario Rossi', 'Via Roma 1', 'Milano', '20100', 'ITA');
        $shipping = new Address('Luigi Bianchi', 'Via Torino 2', 'Roma', '00100', 'ITA');

        $info = new CustomerInfo(
            'Mario Rossi',
            'mario@example.com',
            $billing,
            $shipping,
            '39',
            '3331234567',
            '0212345678',
            '0298765432'
        );

        $result = $info->toArray();

        $this->assertSame('Mario Rossi', $result['cardHolderName']);
        $this->assertSame('mario@example.com', $result['cardHolderEmail']);
        $this->assertArrayHasKey('billingAddress', $result);
        $this->assertArrayHasKey('shippingAddress', $result);
        $this->assertSame('Via Roma 1', $result['billingAddress']['street']);
        $this->assertSame('Via Torino 2', $result['shippingAddress']['street']);
        $this->assertSame('39', $result['mobilePhoneCountryCode']);
        $this->assertSame('3331234567', $result['mobilePhone']);
        $this->assertSame('0212345678', $result['homePhone']);
        $this->assertSame('0298765432', $result['workPhone']);
    }

    public function testToArrayOmitsNullFields(): void
    {
        $info = new CustomerInfo('Mario Rossi');

        $result = $info->toArray();

        $this->assertArrayHasKey('cardHolderName', $result);
        $this->assertArrayNotHasKey('cardHolderEmail', $result);
        $this->assertArrayNotHasKey('billingAddress', $result);
        $this->assertArrayNotHasKey('shippingAddress', $result);
        $this->assertArrayNotHasKey('mobilePhoneCountryCode', $result);
        $this->assertArrayNotHasKey('mobilePhone', $result);
        $this->assertArrayNotHasKey('homePhone', $result);
        $this->assertArrayNotHasKey('workPhone', $result);
    }

    public function testToArrayWithPhoneFieldsOnly(): void
    {
        $info = new CustomerInfo(null, null, null, null, '39', '3339876543');

        $result = $info->toArray();

        $this->assertSame('39', $result['mobilePhoneCountryCode']);
        $this->assertSame('3339876543', $result['mobilePhone']);
        $this->assertArrayNotHasKey('homePhone', $result);
        $this->assertArrayNotHasKey('workPhone', $result);
    }
}
