<?php declare(strict_types=1);

namespace Hval\Nexi;

use Hval\Nexi\Http\HttpFactoryInterface;
use Hval\Nexi\Service\OperationService;
use Hval\Nexi\Service\OrderService;
use Hval\Nexi\Webhook\WebhookHandler;
use Psr\Http\Client\ClientInterface;

class NexiClient
{
    const ENV_PRODUCTION = 'production';
    const ENV_SANDBOX = 'sandbox';

    const BASE_URLS = [
        self::ENV_PRODUCTION => 'https://xpay.nexigroup.com/api/phoenix-0.0/psp/api/v1',
        self::ENV_SANDBOX => 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1',
    ];

    /** @var OrderService */
    private $orders;

    /** @var OperationService */
    private $operations;

    /** @var WebhookHandler */
    private $webhookHandler;

    public function __construct(
        string $apiKey,
        string $environment,
        ClientInterface $httpClient,
        HttpFactoryInterface $factory
    ) {
        $baseUrl = self::BASE_URLS[$environment] ?? self::BASE_URLS[self::ENV_PRODUCTION];

        $this->orders = new OrderService($httpClient, $factory, $apiKey, $baseUrl);
        $this->operations = new OperationService($httpClient, $factory, $apiKey, $baseUrl);
        $this->webhookHandler = new WebhookHandler($apiKey);
    }

    public function orders(): OrderService
    {
        return $this->orders;
    }

    public function operations(): OperationService
    {
        return $this->operations;
    }

    public function webhooks(): WebhookHandler
    {
        return $this->webhookHandler;
    }
}
