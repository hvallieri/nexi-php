<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Response\ContractsByCustomerResponse;
use Psr\Http\Client\ClientExceptionInterface;

class ContractService extends AbstractService
{
    /**
     * Retrieves all recurring contracts associated with a customer.
     *
     * @see https://developer.nexi.it/en/api/get-contracts-customers-customerid
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function findByCustomer(string $customerId): ContractsByCustomerResponse
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/contracts/customers/' . rawurlencode($customerId))
        );

        return ContractsByCustomerResponse::fromArray($data);
    }

    /**
     * Deactivates a recurring contract.
     *
     * @see https://developer.nexi.it/en/api/post-contracts-contractid-deactivation
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function deactivate(string $contractId): void
    {
        $this->parseResponse(
            $this->post($this->baseUrl . '/contracts/' . rawurlencode($contractId) . '/deactivation', '')
        );
    }
}
