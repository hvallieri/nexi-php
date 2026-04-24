<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\RefundRequest;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers RefundRequest
 */
class RefundRequestTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $request = new RefundRequest('500', 'EUR');

        $this->assertSame([
            'amount' => '500',
            'currency' => 'EUR',
        ], $request->toArray());
    }

    public function testToArrayWithDescription(): void
    {
        $request = new RefundRequest('500', 'EUR', 'Reso parziale');

        $result = $request->toArray();

        $this->assertSame('Reso parziale', $result['description']);
    }

    public function testToArrayOmitsNullDescription(): void
    {
        $request = new RefundRequest('500', 'EUR');

        $this->assertArrayNotHasKey('description', $request->toArray());
    }
}
