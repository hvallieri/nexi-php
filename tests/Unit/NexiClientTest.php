<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit;

use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\NexiClient;
use Hval\Nexi\Service\OperationService;
use Hval\Nexi\Service\OrderService;
use Hval\Nexi\Webhook\WebhookHandler;
use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

/**
 * @internal
 * @covers NexiClient
 */
class NexiClientTest extends TestCase
{
    /** @var NexiClient */
    private $client;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->client = new NexiClient(
            'test-api-key',
            NexiClient::ENV_SANDBOX,
            $this->createMock(ClientInterface::class),
            new HttpFactory($psr17, $psr17)
        );
    }

    public function testOrdersReturnsOrderService(): void
    {
        $this->assertInstanceOf(OrderService::class, $this->client->orders());
    }

    public function testOperationsReturnsOperationService(): void
    {
        $this->assertInstanceOf(OperationService::class, $this->client->operations());
    }

    public function testWebhooksReturnsWebhookHandler(): void
    {
        $this->assertInstanceOf(WebhookHandler::class, $this->client->webhooks());
    }

    public function testUnknownEnvironmentThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $psr17 = new Psr17Factory();
        new NexiClient(
            'test-api-key',
            'unknown-env',
            $this->createMock(ClientInterface::class),
            new HttpFactory($psr17, $psr17)
        );
    }

    public function testSandboxAndProductionAreDistinctEnvironments(): void
    {
        $this->assertNotSame(
            NexiClient::BASE_URLS[NexiClient::ENV_SANDBOX],
            NexiClient::BASE_URLS[NexiClient::ENV_PRODUCTION]
        );
    }
}
