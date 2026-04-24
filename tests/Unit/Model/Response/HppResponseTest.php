<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\HppResponse;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers HppResponse
 */
class HppResponseTest extends TestCase
{
    public function testFromArray(): void
    {
        $response = HppResponse::fromArray([
            'hostedPage' => 'https://gateway.example.com/pay/abc123',
            'securityToken' => 'a1b2c3d4e5f6',
        ]);

        $this->assertSame('https://gateway.example.com/pay/abc123', $response->getHostedPage());
        $this->assertSame('a1b2c3d4e5f6', $response->getSecurityToken());
    }

    public function testFromArrayWithMissingKeysFallsBackToEmptyString(): void
    {
        $response = HppResponse::fromArray([]);

        $this->assertSame('', $response->getHostedPage());
        $this->assertSame('', $response->getSecurityToken());
    }
}
