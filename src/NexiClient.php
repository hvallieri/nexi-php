<?php declare(strict_types=1);

namespace Hval\Nexi;

use Hval\Nexi\Enum\Environment;
use Hval\Nexi\Service\ContractService;
use Hval\Nexi\Service\OperationService;
use Hval\Nexi\Service\OrderService;
use Hval\Nexi\Service\PayByLinkService;
use Hval\Nexi\Service\PaymentMethodService;
use Hval\Nexi\Webhook\WebhookHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class NexiClient
{
    private readonly OrderService $orders;
    private readonly OperationService $operations;
    private readonly ContractService $contracts;
    private readonly PayByLinkService $payByLink;
    private readonly PaymentMethodService $paymentMethods;
    private readonly WebhookHandler $webhookHandler;

    public function __construct(
        string $apiKey,
        Environment $environment,
        ClientInterface $httpClient,
        RequestFactoryInterface&StreamFactoryInterface $factory,
    ) {
        $baseUrl = $environment->baseUrl();

        $this->orders = new OrderService($httpClient, $factory, $apiKey, $baseUrl);
        $this->operations = new OperationService($httpClient, $factory, $apiKey, $baseUrl);
        $this->contracts = new ContractService($httpClient, $factory, $apiKey, $baseUrl);
        $this->payByLink = new PayByLinkService($httpClient, $factory, $apiKey, $baseUrl);
        $this->paymentMethods = new PaymentMethodService($httpClient, $factory, $apiKey, $baseUrl);
        $this->webhookHandler = new WebhookHandler();
    }

    /**
     * Order creation (HPP) and retrieval.
     *
     * @see https://developer.nexi.it/en/api/post-orders-hpp
     * @see https://developer.nexi.it/en/api/get-orders-orderId
     */
    public function orders(): OrderService
    {
        return $this->orders;
    }

    /**
     * Post-payment operations: refund, capture, cancel.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationId-refunds
     * @see https://developer.nexi.it/en/api/post-operations-operationId-captures
     * @see https://developer.nexi.it/en/api/post-operations-operationId-cancels
     */
    public function operations(): OperationService
    {
        return $this->operations;
    }

    /**
     * Recurring contract retrieval and deactivation.
     *
     * @see https://developer.nexi.it/en/api/get-contracts-customers-customerId
     */
    public function contracts(): ContractService
    {
        return $this->contracts;
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
     * @see https://developer.nexi.it/en/api/notifica
     */
    public function webhooks(): WebhookHandler
    {
        return $this->webhookHandler;
    }
}
