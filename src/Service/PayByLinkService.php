<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\PayByLinkResponse;
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
