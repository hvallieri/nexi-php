<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\CaptureRequest;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers CaptureRequest
 */
class CaptureRequestTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $request = new CaptureRequest(3545, 'EUR');

        $this->assertSame([
            'amount' => 3545,
            'currency' => 'EUR',
        ], $request->toArray());
    }

    public function testToArrayWithDescription(): void
    {
        $request = new CaptureRequest(3545, 'EUR', 'Cattura ordine TV');

        $result = $request->toArray();

        $this->assertSame('Cattura ordine TV', $result['description']);
    }

    public function testToArrayOmitsNullDescription(): void
    {
        $request = new CaptureRequest(3545, 'EUR');

        $this->assertArrayNotHasKey('description', $request->toArray());
    }
}
