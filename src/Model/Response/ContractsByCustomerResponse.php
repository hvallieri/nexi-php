<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class ContractsByCustomerResponse implements ResponseModelInterface
{
    /** @var string|null */
    private $customerId;

    /** @var array<int, ContractSummary> */
    private $contracts;

    /**
     * @param array<int, ContractSummary> $contracts
     */
    public function __construct(?string $customerId, array $contracts)
    {
        $this->customerId = $customerId;
        $this->contracts = $contracts;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * @return array<int, ContractSummary>
     */
    public function getContracts(): array
    {
        return $this->contracts;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $contracts = [];

        if (isset($data['contracts']) && is_array($data['contracts']) === true) {
            foreach ($data['contracts'] as $item) {
                if (is_array($item) === true) {
                    $contracts[] = ContractSummary::fromArray($item);
                }
            }
        }

        return new self($data['customerId'] ?? null, $contracts);
    }
}
