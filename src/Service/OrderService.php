<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\HppResponse;
use Hval\Nexi\Model\Response\OrderResponse;
use Psr\Http\Client\ClientExceptionInterface;

class OrderService extends AbstractService
{
    /**
     * Creates an order and returns the Hosted Payment Page URL.
     *
     * @see https://developer.nexi.it/en/api/post-orders-hpp
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function createHpp(Order $order, PaymentSession $paymentSession): HppResponse
    {
        $body = json_encode([
            'order' => $order->toArray(),
            'paymentSession' => $paymentSession->toArray(),
        ]);

        if ($body === false) {
            throw new NexiException('Failed to encode request body.');
        }

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/orders/hpp', $body)
        );

        return HppResponse::fromArray($data);
    }

    /**
     * Retrieves the status of an order by its orderId.
     *
     * @see https://developer.nexi.it/en/api/get-orders-orderid
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function find(string $orderId): OrderResponse
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders/' . rawurlencode($orderId))
        );

        return OrderResponse::fromArray($data);
    }
}
