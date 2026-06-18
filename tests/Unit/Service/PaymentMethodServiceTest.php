<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Model\Response\PaymentMethod;
use Hval\Nexi\Service\PaymentMethodService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @covers PaymentMethodService
 */
class PaymentMethodServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var PaymentMethodService */
    private $service;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->service = new PaymentMethodService($this->httpClient, new HttpFactory($psr17, $psr17), self::API_KEY, self::BASE_URL);
    }

    public function testListAllReturnsArrayOfPaymentMethod(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'paymentMethods' => [
                    ['methodType' => 'CARD', 'circuit' => 'VISA', 'imageLink' => 'https://example.com/visa.svg', 'recurringSupported' => true, 'oneClickSupported' => true],
                    ['methodType' => 'CARD', 'circuit' => 'MC', 'imageLink' => 'https://example.com/mc.svg', 'recurringSupported' => false, 'oneClickSupported' => false],
                ],
            ])))
        ;

        $result = $this->service->listAll();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(PaymentMethod::class, $result[0]);
        $this->assertSame('VISA', $result[0]->getCircuit());
        $this->assertSame('MC', $result[1]->getCircuit());
    }

    public function testListAllCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/payment_methods') !== false
                    && strpos((string) $request->getUri(), '?') === false;
            }))
            ->willReturn(new Response(200, [], json_encode(['paymentMethods' => []])))
        ;

        $this->service->listAll();
    }

    public function testListAllReturnsEmptyArrayWhenKeyMissing(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([])))
        ;

        $this->assertSame([], $this->service->listAll());
    }

    public function testListAllThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->listAll();
    }
}
