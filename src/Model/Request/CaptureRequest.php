<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class CaptureRequest implements RequestModelInterface
{
    /** @var string|null */
    private $amount;

    /** @var string|null */
    private $currency;

    /** @var string|null */
    private $description;

    /**
     * Omitting both amount and currency triggers a full capture on the Nexi side.
     * If amount is provided, currency must be provided as well.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationId-captures
     */
    public function __construct(
        ?string $amount = null,
        ?string $currency = null,
        ?string $description = null
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
