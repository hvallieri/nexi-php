<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\CancelRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CancelRequest::class)]
class CancelRequestTest extends TestCase
{
    public function testToArrayWithNoDescription(): void
    {
        $request = new CancelRequest();

        $this->assertSame([], $request->toArray());
    }

    public function testToArrayWithDescription(): void
    {
        $request = new CancelRequest('Annullato dal cliente');

        $this->assertSame(['description' => 'Annullato dal cliente'], $request->toArray());
    }
}
