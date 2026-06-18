<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\PaymentMethod;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers PaymentMethod
 */
class PaymentMethodTest extends TestCase
{
    public function testFromArrayPopulatesAllFields(): void
    {
        $method = PaymentMethod::fromArray([
            'methodType' => 'CARD',
            'circuit' => 'VISA',
            'imageLink' => 'https://example.com/visa.svg',
            'recurringSupported' => true,
            'oneClickSupported' => false,
        ]);

        $this->assertSame('CARD', $method->getMethodType());
        $this->assertSame('VISA', $method->getCircuit());
        $this->assertSame('https://example.com/visa.svg', $method->getImageLink());
        $this->assertTrue($method->isRecurringSupported());
        $this->assertFalse($method->isOneClickSupported());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $method = PaymentMethod::fromArray([]);

        $this->assertNull($method->getMethodType());
        $this->assertNull($method->getCircuit());
        $this->assertNull($method->getImageLink());
        $this->assertNull($method->isRecurringSupported());
        $this->assertNull($method->isOneClickSupported());
    }

    public function testFromArrayCastsBooleans(): void
    {
        $method = PaymentMethod::fromArray([
            'recurringSupported' => 1,
            'oneClickSupported' => 0,
        ]);

        $this->assertTrue($method->isRecurringSupported());
        $this->assertFalse($method->isOneClickSupported());
    }
}
