<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\PayByLinkResponse;
use Hval\Nexi\Model\Response\PaymentLink;
use Psr\Http\Client\ClientExceptionInterface;

class PayByLinkService extends AbstractService
{
    /**
     * Creates a Pay-by-Link payment link.
     *
     * @see https://developer.nexi.it/en/api/post-orders-paybylink
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function create(Order $order, PaymentSession $session, string $expirationDate): PayByLinkResponse
    {
        $sessionData = $session->toArray();
        $sessionData['expirationDate'] = $expirationDate;

        $body = json_encode([
            'order' => $order->toArray(),
            'paymentSession' => $sessionData,
        ]);

        if ($body === false) {
            throw new NexiException('Failed to encode Pay-by-Link request body.');
        }

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/orders/paybylink', $body)
        );

        return PayByLinkResponse::fromArray($data);
    }

    /**
     * Retrieves a list of Pay-by-Link payment links, optionally filtered by time range and status.
     * Date parameters must be in ISO 8601 format (e.g. 2022-01-01T13:10:00.000Z).
     * The API enforces a maximum interval of one month between fromTime and toTime.
     * Items in the list do not carry a securityToken.
     *
     * @see https://developer.nexi.it/en/api/get-orders-paybylink
     *
     * @param string|null $status one of the PaymentLink::STATUS_* constants
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     *
     * @return array<int, PaymentLink>
     */
    public function findAll(
        ?string $fromTime = null,
        ?string $toTime = null,
        ?int $maxRecords = null,
        ?string $status = null
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

        if ($status !== null) {
            $params['status'] = $status;
        }

        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders/paybylink', $params)
        );

        $items = isset($data['paymentLinks']) && is_array($data['paymentLinks']) ? $data['paymentLinks'] : [];

        $result = [];

        foreach ($items as $item) {
            if (is_array($item) === true) {
                $result[] = PaymentLink::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * Retrieves a Pay-by-Link payment link by its linkId.
     *
     * @see https://developer.nexi.it/en/api/get-orders-paybylink-linkid
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function find(string $linkId): PayByLinkResponse
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/orders/paybylink/' . rawurlencode($linkId))
        );

        return PayByLinkResponse::fromArray($data);
    }

    /**
     * Renews an existing Pay-by-Link payment link, optionally with a new expiration date
     * (maximum 90 days after the creation of the original link).
     *
     * @see https://developer.nexi.it/en/api/post-orders-paybylink-linkid-renewals
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function renew(string $linkId, ?string $expirationDate = null): PayByLinkResponse
    {
        $body = '{}';

        if ($expirationDate !== null) {
            $body = json_encode(['expirationDate' => $expirationDate]);

            if ($body === false) {
                throw new NexiException('Failed to encode Pay-by-Link renewal request body.');
            }
        }

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/orders/paybylink/' . rawurlencode($linkId) . '/renewals', $body)
        );

        return PayByLinkResponse::fromArray($data);
    }

    /**
     * Cancels an active Pay-by-Link payment link.
     *
     * @see https://developer.nexi.it/en/api/post-paybylink-linkid-cancels
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function cancel(string $linkId): void
    {
        $this->parseResponse(
            $this->post($this->baseUrl . '/paybylink/' . rawurlencode($linkId) . '/cancels', '')
        );
    }
}
