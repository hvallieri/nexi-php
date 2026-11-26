<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit;

use Hval\Nexi\Enum\Environment;
use Hval\Nexi\NexiClient;
use Hval\Nexi\Service\ContractService;
use Hval\Nexi\Service\OperationService;
use Hval\Nexi\Service\OrderService;
use Hval\Nexi\Service\PayByLinkService;
use Hval\Nexi\Service\PaymentMethodService;
use Hval\Nexi\Webhook\WebhookHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

/**
 * @internal
 */
#[CoversClass(NexiClient::class)]
class NexiClientTest extends TestCase
{
    /** @var NexiClient */
    private $client;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->client = new NexiClient(
            'test-api-key',
            Environment::Sandbox,
            $this->createStub(ClientInterface::class),
            $psr17,
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

    public function testContractsReturnsContractService(): void
    {
        $this->assertInstanceOf(ContractService::class, $this->client->contracts());
    }

    public function testPayByLinkReturnsPayByLinkService(): void
    {
        $this->assertInstanceOf(PayByLinkService::class, $this->client->payByLink());
    }

    public function testPaymentMethodsReturnsPaymentMethodService(): void
    {
        $this->assertInstanceOf(PaymentMethodService::class, $this->client->paymentMethods());
    }

    public function testWebhooksReturnsWebhookHandler(): void
    {
        $this->assertInstanceOf(WebhookHandler::class, $this->client->webhooks());
    }
}
