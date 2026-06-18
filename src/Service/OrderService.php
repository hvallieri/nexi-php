<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\HppResponse;
use Hval\Nexi\Model\Response\OrderResponse;
use Hval\Nexi\Model\Response\OrderSummary;
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
     * Retrieves a list of orders, optionally filtered by time range and custom field.
     * Date parameters must be in ISO 8601 format (e.g. 2022-01-01T13:10:00.000Z).
     * The API enforces a maximum interval of one month between fromTime and toTime.
     *
     * @see https://developer.nexi.it/en/api/get-orders
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     *
     * @return array<int, OrderSummary>
     */
    public function findAll(
        ?string $fromTime = null,
        ?string $toTime = null,
        ?int $maxRecords = null,
        ?string $customField = null
    ): array {
        $params = [];

        if ($fromTime !== null) {
            $params['fromTime'] = $fromTime;
        }

        if ($toTime !== null) {
            $params['toTime'] = $toTime;
        }

        if ($maxRecords !== null) {
            $params['maxRecords'] = $maxRecords;
        }

        if ($customField !== null) {
            $params['customField'] = $customField;
        }

        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders', $params)
        );

        $result = [];

        foreach ($data as $item) {
            if (is_array($item) === true) {
                $result[] = OrderSummary::fromArray($item);
            }
        }

        return $result;
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
