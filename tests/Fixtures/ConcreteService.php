<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Fixtures;

use Hval\Nexi\Service\AbstractService;
use Psr\Http\Client\ClientExceptionInterface;

final class ConcreteService extends AbstractService
{
    /**
     * @param string $url
     * @param array<string, mixed> $queryParams
     *
     * @throws ClientExceptionInterface
     *
     * @return array{status: int, body: string}
     */
    public function getPublic(string $url, array $queryParams = []): array
    {
        return $this->get($url, $queryParams);
    }
}
