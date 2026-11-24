<?php declare(strict_types=1);

namespace Hval\Nexi\Webhook;

use Hval\Nexi\Exception\WebhookSignatureException;
use Hval\Nexi\Utils\Extractor;

class WebhookHandler
{
    /**
     * Validates and decodes an incoming webhook notification from Nexi.
     *
     * Nexi includes a securityToken in the payload that must match the one
     * returned by the POST /orders/hpp call. Verification is the merchant's
     * responsibility: compare the received securityToken against the one
     * stored at order-creation time in your own database.
     *
     * @param string $rawBody Raw JSON body of the HTTP request
     * @param string $savedToken The securityToken stored at order creation
     *
     * @throws WebhookSignatureException if the payload is not a valid JSON object or the token does not match
     *
     * @return WebhookNotification
     */
    public function handle(string $rawBody, string $savedToken): WebhookNotification
    {
        $data = Extractor::fromJsonOrFail($rawBody, new WebhookSignatureException('Invalid webhook payload: not valid JSON.'));
        $receivedToken = Extractor::string($data, 'securityToken') ?? '';

        if (hash_equals($savedToken, $receivedToken) === false) {
            throw new WebhookSignatureException('Webhook securityToken mismatch.');
        }

        return WebhookNotification::fromArray($data);
    }
}
