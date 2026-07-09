<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\HppResponse;
use Hval\Nexi\Model\Response\OperationDetails;
use Hval\Nexi\Model\Response\OrderResponse;
use Hval\Nexi\Model\Response\OrderSummary;
use Psr\Http\Client\ClientExceptionInterface;

class OrderService extends AbstractService
{
    const AMOUNT_TYPE_ORDER_AMOUNT = 'ORDER_AMOUNT';
    const AMOUNT_TYPE_AUTHORIZED_AMOUNT = 'AUTHORIZED_AMOUNT';
    const AMOUNT_TYPE_CAPTURED_AMOUNT = 'CAPTURED_AMOUNT';

    const ORDER_STATE_TO_CAPTURE = 'TO_CAPTURE';
    const ORDER_STATE_CAPTURED = 'CAPTURED';

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
     * Charges a previously created recurring contract (MIT — Merchant Initiated Transaction),
     * used for subscription-like services.
     *
     * @see https://developer.nexi.it/en/api/post-orders-mit
     *
     * @param string|null $captureType one of the PaymentSession::CAPTURE_* constants; overwrites the terminal default
     * @param string|null $idempotencyKey UUID v4 — supply your own key to make retries safe; auto-generated if omitted
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function createMit(
        Order $order,
        string $contractId,
        ?string $captureType = null,
        ?string $idempotencyKey = null
    ): OperationDetails {
        $payload = [
            'order' => $order->toArray(),
            'contractId' => $contractId,
        ];

        if ($captureType !== null) {
            $payload['captureType'] = $captureType;
        }

        $body = json_encode($payload);

        if ($body === false) {
            throw new NexiException('Failed to encode MIT request body.');
        }

        $data = $this->parseResponse(
            $this->post(
                $this->baseUrl . '/orders/mit',
                $body,
                ['Idempotency-Key' => $idempotencyKey ?? $this->generateIdempotencyKey()]
            )
        );

        $operation = isset($data['operation']) && is_array($data['operation']) ? $data['operation'] : [];

        return OperationDetails::fromArray($operation);
    }

    /**
     * Retrieves a list of orders, optionally filtered by time range, custom field,
     * orderId, amount range and order state.
     * Date parameters must be in ISO 8601 format (e.g. 2022-01-01T13:10:00.000Z).
     * The API enforces a maximum interval of one month between fromTime and toTime.
     * When amountType is specified (see the AMOUNT_TYPE_* constants), the API requires
     * both minAmount and maxAmount.
     *
     * @see https://developer.nexi.it/en/api/get-orders
     *
     * @param string|null $amountType one of the AMOUNT_TYPE_* constants
     * @param string|null $orderState one of the ORDER_STATE_* constants
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
        ?string $customField = null,
        ?string $orderId = null,
        ?string $amountType = null,
        ?string $minAmount = null,
        ?string $maxAmount = null,
        ?string $orderState = null
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

        if ($orderId !== null) {
            $params['orderId'] = $orderId;
        }

        if ($amountType !== null) {
            $params['amountType'] = $amountType;
        }

        if ($minAmount !== null) {
            $params['minAmount'] = $minAmount;
        }

        if ($maxAmount !== null) {
            $params['maxAmount'] = $maxAmount;
        }

        if ($orderState !== null) {
            $params['orderState'] = $orderState;
        }

        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders', $params)
        );

        $items = isset($data['orders']) && is_array($data['orders']) ? $data['orders'] : [];

        $result = [];

        foreach ($items as $item) {
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
