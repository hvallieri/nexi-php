<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Service\AbstractService;
use Hval\Nexi\Tests\Fixtures\ConcreteService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 */
#[CoversClass(AbstractService::class)]
class AbstractServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';

    private ClientInterface&MockObject $httpClient;
    private ConcreteService $service;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->service = new ConcreteService($this->httpClient, new Psr17Factory(), self::API_KEY, self::BASE_URL);
    }

    public function testGetWithoutQueryParamsBuildsUrlWithoutQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(fn (RequestInterface $request): bool => (string) $request->getUri() === self::BASE_URL . '/orders'))
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

                return str_contains($uri, 'fromTime=2024-01-01') === true
                    && str_contains($uri, 'maxRecords=10') === true;
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
            ->with($this->callback(fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '?') === false))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->getPublic(self::BASE_URL . '/orders', []);
    }
}
