<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class RefundRequest implements RequestModelInterface
{
    private ?string $amount;
    private ?string $currency;
    private ?string $description;

    /**
     * Nexi requires the amount: a request without it is rejected (HTTP 500, GW0001).
     * To refund the whole captured amount, pass it explicitly.
     * If amount is provided, currency must be provided as well.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationId-refunds
     */
    public function __construct(
        ?string $amount = null,
        ?string $currency = null,
        ?string $description = null,
    ) {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->amount !== null) {
            $data['amount'] = $this->amount;
        }

        if ($this->currency !== null) {
            $data['currency'] = $this->currency;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
