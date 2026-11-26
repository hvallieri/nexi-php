<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Enum;

use Hval\Nexi\Enum\Environment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Environment::class)]
class EnvironmentTest extends TestCase
{
    public function testProductionBaseUrl(): void
    {
        $this->assertSame('https://xpay.nexigroup.com/api/phoenix-0.0/psp/api/v1', Environment::Production->baseUrl());
    }

    public function testSandboxBaseUrl(): void
    {
        $this->assertSame('https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1', Environment::Sandbox->baseUrl());
    }

    public function testFromConfigurationValue(): void
    {
        $this->assertSame(Environment::Sandbox, Environment::from('sandbox'));
        $this->assertSame(Environment::Production, Environment::from('production'));
    }
}
