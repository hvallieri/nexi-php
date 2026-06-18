<?php declare(strict_types=1);

namespace Hval\Nexi;

use Hval\Nexi\Http\HttpFactoryInterface;
use Hval\Nexi\Service\OperationService;
use Hval\Nexi\Service\OrderService;
use Hval\Nexi\Service\PayByLinkService;
use Hval\Nexi\Service\PaymentMethodService;
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

    /** @var PayByLinkService */
    private $payByLink;

    /** @var PaymentMethodService */
    private $paymentMethods;

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
        $this->payByLink = new PayByLinkService($httpClient, $factory, $apiKey, self::BASE_URLS[$environment]);
        $this->paymentMethods = new PaymentMethodService($httpClient, $factory, $apiKey, self::BASE_URLS[$environment]);
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
     * Pay-by-Link creation and cancellation.
     *
     * @see https://developer.nexi.it/en/api/post-orders-paybylink
     */
    public function payByLink(): PayByLinkService
    {
        return $this->payByLink;
    }

    /**
     * Payment methods supported by the merchant's contract.
     *
     * @see https://developer.nexi.it/en/api/get-payment_methods
     */
    public function paymentMethods(): PaymentMethodService
    {
        return $this->paymentMethods;
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
