<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class HppResponse implements ResponseModelInterface
{
    private ?string $hostedPage;
    private ?string $securityToken;

    public function __construct(?string $hostedPage, ?string $securityToken)
    {
        $this->hostedPage = $hostedPage;
        $this->securityToken = $securityToken;
    }

    public function getHostedPage(): ?string
    {
        return $this->hostedPage;
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
            hostedPage: $data['hostedPage'] ?? null,
            securityToken: $data['securityToken'] ?? null,
        );
    }
}
