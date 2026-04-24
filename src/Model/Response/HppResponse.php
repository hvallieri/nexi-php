<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class HppResponse implements ResponseModelInterface
{
    /** @var string */
    private $hostedPage;

    /** @var string */
    private $securityToken;

    public function __construct(string $hostedPage, string $securityToken)
    {
        $this->hostedPage = $hostedPage;
        $this->securityToken = $securityToken;
    }

    public function getHostedPage(): string
    {
        return $this->hostedPage;
    }

    public function getSecurityToken(): string
    {
        return $this->securityToken;
    }

    /**
     * @param array{hostedPage: string, securityToken: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['hostedPage'] ?? ''),
            (string) ($data['securityToken'] ?? '')
        );
    }
}
