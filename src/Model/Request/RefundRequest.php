<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class RefundRequest implements RequestModelInterface
{
    /** @var int */
    private $amount;

    /** @var string */
    private $currency;

    /** @var string|null */
    private $description;

    public function __construct(int $amount, string $currency, ?string $description = null)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
