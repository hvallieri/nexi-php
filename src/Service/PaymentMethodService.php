<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Response\PaymentMethod;
use Psr\Http\Client\ClientExceptionInterface;

class PaymentMethodService extends AbstractService
{
    /**
     * Retrieves the list of payment methods supported by the merchant's contract.
     *
     * @see https://developer.nexi.it/en/api/get-payment_methods
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     *
     * @return array<int, PaymentMethod>
     */
    public function listAll(): array
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/payment_methods')
        );

        $items = isset($data['paymentMethods']) && is_array($data['paymentMethods']) ? $data['paymentMethods'] : [];

        $result = [];

        foreach ($items as $item) {
            if (is_array($item) === true) {
                $result[] = PaymentMethod::fromArray($item);
            }
        }

        return $result;
    }
}
