<?php declare(strict_types=1);

namespace Hval\Nexi;

use Hval\Nexi\Http\HttpFactoryInterface;
use Hval\Nexi\Service\OperationService;
use Hval\Nexi\Service\OrderService;
use Hval\Nexi\Webhook\WebhookHandler;
use InvalidArgumentException;
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
        if (isset(self::BASE_URLS[$environment]) === false) {
            throw new InvalidArgumentException($environment . ' is not a valid Environment');
        }

        $this->orders = new OrderService($httpClient, $factory, $apiKey, self::BASE_URLS[$environment]);
        $this->operations = new OperationService($httpClient, $factory, $apiKey, self::BASE_URLS[$environment]);
        $this->webhookHandler = new WebhookHandler();
    }

    /**
     * Order creation (HPP) and retrieval.
     *
     * @see https://developer.nexi.it/it/api/post-orders-hpp
     * @see https://developer.nexi.it/it/api/get-orders-orderId
     */
    public function orders(): OrderService
    {
        return $this->orders;
    }

    /**
     * Post-payment operations: refund, capture, cancel.
     *
     * @see https://developer.nexi.it/it/api/post-operations-operationId-refunds
     * @see https://developer.nexi.it/it/api/post-operations-operationId-captures
     * @see https://developer.nexi.it/it/api/post-operations-operationId-cancels
     */
    public function operations(): OperationService
    {
        return $this->operations;
    }

    /**
     * Incoming webhook verification and parsing.
     *
     * @see https://developer.nexi.it/it/api/notifica
     */
    public function webhooks(): WebhookHandler
    {
        return $this->webhookHandler;
    }
}
