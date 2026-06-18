<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Service\AbstractService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @covers AbstractService
 */
class AbstractServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var AbstractService */
    private $service;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->httpClient = $this->createMock(ClientInterface::class);

        $this->service = new class($this->httpClient, new HttpFactory($psr17, $psr17), self::API_KEY, self::BASE_URL) extends AbstractService {
            public function getPublic(string $url, array $queryParams = []): array
            {
                return $this->get($url, $queryParams);
            }
        };
    }

    public function testGetWithoutQueryParamsBuildsUrlWithoutQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return (string) $request->getUri() === self::BASE_URL . '/orders';
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->getPublic(self::BASE_URL . '/orders');
    }

    public function testGetWithQueryParamsAppendsQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return strpos($uri, 'fromTime=2024-01-01') !== false
                    && strpos($uri, 'maxRecords=10') !== false;
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->getPublic(self::BASE_URL . '/orders', [
            'fromTime' => '2024-01-01',
            'maxRecords' => 10,
        ]);
    }

    public function testGetWithEmptyQueryParamsBuildsUrlWithoutQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '?') === false;
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->getPublic(self::BASE_URL . '/orders', []);
    }
}
