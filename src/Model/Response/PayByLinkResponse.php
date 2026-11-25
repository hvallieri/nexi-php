<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

final class PayByLinkResponse implements ResponseModelInterface
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
     * Nexi returns the securityToken at the root of the response, next to paymentLink:
     * it is passed to the PaymentLink so that PaymentLink::getSecurityToken() returns it.
     *
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $paymentLink = null;

        if (isset($data['paymentLink']) && is_array($data['paymentLink'])) {
            $paymentLink = PaymentLink::fromArray($data['paymentLink'] + ['securityToken' => $data['securityToken'] ?? null]);
        }

        return new self($paymentLink);
    }
}
