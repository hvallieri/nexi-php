<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Enum\AmountType;
use Hval\Nexi\Enum\CaptureType;
use Hval\Nexi\Enum\OrderState;
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
    /**
     * Creates an order and returns the Hosted Payment Page URL.
     *
     * @see https://developer.nexi.it/en/api/post-orders-hpp
     *
     * @param Order $order
     * @param PaymentSession $paymentSession
     *
     * @throws ClientExceptionInterface
     *
     * @return HppResponse
     */
    public function createHpp(Order $order, PaymentSession $paymentSession): HppResponse
    {
        $body = $this->encodeBody([
            'order' => $order->toArray(),
            'paymentSession' => $paymentSession->toArray(),
        ], 'Failed to encode request body.');

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/orders/hpp', $body),
        );

        return HppResponse::fromArray($data);
    }

    /**
     * Charges a previously created recurring contract (MIT — Merchant Initiated Transaction),
     * used for subscription-like services.
     *
     * @see https://developer.nexi.it/en/api/post-orders-mit
     *
     * @param Order $order
     * @param string $contractId
     * @param CaptureType|null $captureType overwrites the terminal default
     * @param string|null $idempotencyKey UUID v4 — supply your own key to make retries safe; auto-generated if omitted
     *
     * @throws ClientExceptionInterface
     *
     * @return OperationDetails
     */
    public function createMit(
        Order $order,
        string $contractId,
        ?CaptureType $captureType = null,
        ?string $idempotencyKey = null,
    ): OperationDetails {
        $payload = [
            'order' => $order->toArray(),
            'contractId' => $contractId,
        ];

        if ($captureType !== null) {
            $payload['captureType'] = $captureType->value;
        }

        $body = $this->encodeBody($payload, 'Failed to encode MIT request body.');

        $data = $this->parseResponse(
            $this->post(
                $this->baseUrl . '/orders/mit',
                $body,
                ['Idempotency-Key' => $idempotencyKey ?? $this->generateIdempotencyKey()],
            ),
        );

        $operation = isset($data['operation']) && is_array($data['operation']) ? $data['operation'] : [];

        if (isset($operation['warnings']) === false && isset($data['warnings']) === true) {
            $operation['warnings'] = $data['warnings'];
        }

        return OperationDetails::fromArray($operation);
    }

    /**
     * Retrieves a list of orders, optionally filtered by time range, custom field,
     * orderId, amount range and order state.
     * Date parameters must be in ISO 8601 format (e.g. 2022-01-01T13:10:00.000Z).
     * The API enforces a maximum interval of one month between fromTime and toTime.
     * When amountType is specified, the API requires both minAmount and maxAmount.
     *
     * @see https://developer.nexi.it/en/api/get-orders
     *
     * @param string|null $fromTime
     * @param string|null $toTime
     * @param int|null $maxRecords
     * @param string|null $customField
     * @param string|null $orderId
     * @param AmountType|null $amountType
     * @param string|null $minAmount
     * @param string|null $maxAmount
     * @param OrderState|null $orderState
     *
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
        ?AmountType $amountType = null,
        ?string $minAmount = null,
        ?string $maxAmount = null,
        ?OrderState $orderState = null,
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
            $params['amountType'] = $amountType->value;
        }

        if ($minAmount !== null) {
            $params['minAmount'] = $minAmount;
        }

        if ($maxAmount !== null) {
            $params['maxAmount'] = $maxAmount;
        }

        if ($orderState !== null) {
            $params['orderState'] = $orderState->value;
        }

        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders', $params),
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
     * @see https://developer.nexi.it/en/api/get-orders-orderId
     *
     * @param string $orderId
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     *
     * @return OrderResponse
     */
    public function find(string $orderId): OrderResponse
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders/' . rawurlencode($orderId)),
        );

        return OrderResponse::fromArray($data);
    }
}
