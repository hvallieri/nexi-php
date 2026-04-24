<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\CustomerInfo;
use Hval\Nexi\Model\Request\Order;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers Order
 */
class OrderTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $order = new Order('ORD-001', '1000', 'EUR');

        $this->assertSame([
            'orderId' => 'ORD-001',
            'amount' => '1000',
            'currency' => 'EUR',
        ], $order->toArray());
    }

    public function testToArrayWithOptionalFields(): void
    {
        $order = new Order(
            'ORD-001',
            '1000',
            'EUR',
            'CUST-123',
            'Ordine di prova',
            'promo2024'
        );

        $result = $order->toArray();

        $this->assertSame('CUST-123', $result['customerId']);
        $this->assertSame('Ordine di prova', $result['description']);
        $this->assertSame('promo2024', $result['customField']);
    }

    public function testToArrayOmitsNullOptionalFields(): void
    {
        $order = new Order('ORD-001', '1000', 'EUR');

        $result = $order->toArray();

        $this->assertArrayNotHasKey('customerId', $result);
        $this->assertArrayNotHasKey('description', $result);
        $this->assertArrayNotHasKey('customField', $result);
        $this->assertArrayNotHasKey('customerInfo', $result);
    }

    public function testToArrayIncludesCustomerInfo(): void
    {
        $customerInfo = new CustomerInfo('Mario Rossi', 'mario@example.com');
        $order = new Order('ORD-001', '1000', 'EUR', null, null, null, $customerInfo);

        $result = $order->toArray();

        $this->assertArrayHasKey('customerInfo', $result);
        $this->assertSame('Mario Rossi', $result['customerInfo']['cardHolderName']);
    }
}
