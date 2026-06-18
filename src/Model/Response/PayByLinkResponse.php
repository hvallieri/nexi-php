<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class PayByLinkResponse implements ResponseModelInterface
{
    /** @var PaymentLink|null */
    private $paymentLink;

    public function __construct(?PaymentLink $paymentLink)
    {
        $this->paymentLink = $paymentLink;
    }

    public function getPaymentLink(): ?PaymentLink
    {
        return $this->paymentLink;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $paymentLink = isset($data['paymentLink']) && is_array($data['paymentLink'])
            ? PaymentLink::fromArray($data['paymentLink'])
            : null;

        return new self($paymentLink);
    }
}
