<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class PaymentLink implements ResponseModelInterface
{
    public const string STATUS_ACTIVE = 'ACTIVE';
    public const string STATUS_DELETED = 'DELETED';
    public const string STATUS_EXPIRED = 'EXPIRED';
    public const string STATUS_INACTIVE = 'INACTIVE';

    private ?string $linkId;
    private ?string $amount;
    private ?string $expirationDate;
    private ?string $link;
    private ?string $paidByOperationId;
    private ?string $status;
    private ?string $securityToken;

    public function __construct(
        ?string $linkId,
        ?string $amount,
        ?string $expirationDate,
        ?string $link,
        ?string $paidByOperationId,
        ?string $status,
        ?string $securityToken,
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
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            linkId: $data['linkId'] ?? null,
            amount: $data['amount'] ?? null,
            expirationDate: $data['expirationDate'] ?? null,
            link: $data['link'] ?? null,
            paidByOperationId: $data['paidByOperationId'] ?? null,
            status: $data['status'] ?? null,
            securityToken: $data['securityToken'] ?? null,
        );
    }
}
