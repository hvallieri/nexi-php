<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class PaymentLink implements ResponseModelInterface
{
    /** @var string|null */
    private $linkId;

    /** @var string|null */
    private $amount;

    /** @var string|null */
    private $expirationDate;

    /** @var string|null */
    private $link;

    /** @var string|null */
    private $paidByOperationId;

    /** @var string|null */
    private $status;

    /** @var string|null */
    private $securityToken;

    public function __construct(
        ?string $linkId,
        ?string $amount,
        ?string $expirationDate,
        ?string $link,
        ?string $paidByOperationId,
        ?string $status,
        ?string $securityToken
    ) {
        $this->linkId = $linkId;
        $this->amount = $amount;
        $this->expirationDate = $expirationDate;
        $this->link = $link;
        $this->paidByOperationId = $paidByOperationId;
        $this->status = $status;
        $this->securityToken = $securityToken;
    }

    public function getLinkId(): ?string
    {
        return $this->linkId;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getPaidByOperationId(): ?string
    {
        return $this->paidByOperationId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getSecurityToken(): ?string
    {
        return $this->securityToken;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['linkId'] ?? null,
            $data['amount'] ?? null,
            $data['expirationDate'] ?? null,
            $data['link'] ?? null,
            $data['paidByOperationId'] ?? null,
            $data['status'] ?? null,
            $data['securityToken'] ?? null
        );
    }
}
