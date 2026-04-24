<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\Address;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers Address
 */
class AddressTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $address = new Address('Mario Rossi', 'Via Roma 1', 'Milano', '20100', 'ITA');

        $this->assertSame([
            'name' => 'Mario Rossi',
            'street' => 'Via Roma 1',
            'city' => 'Milano',
            'postCode' => '20100',
            'country' => 'ITA',
        ], $address->toArray());
    }

    public function testToArrayWithOptionalFields(): void
    {
        $address = new Address(
            'Mario Rossi',
            'Via Roma 1',
            'Milano',
            '20100',
            'ITA',
            'MI',
            'Scala B'
        );

        $result = $address->toArray();

        $this->assertSame('MI', $result['province']);
        $this->assertSame('Scala B', $result['additionalInfo']);
    }

    public function testToArrayOmitsNullOptionalFields(): void
    {
        $address = new Address('Mario Rossi', 'Via Roma 1', 'Milano', '20100', 'ITA');

        $result = $address->toArray();

        $this->assertArrayNotHasKey('province', $result);
        $this->assertArrayNotHasKey('additionalInfo', $result);
    }
}
