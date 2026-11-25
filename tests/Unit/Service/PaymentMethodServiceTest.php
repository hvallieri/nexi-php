<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Model\Response\PaymentMethod;
use Hval\Nexi\Service\PaymentMethodService;
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
#[CoversClass(PaymentMethodService::class)]
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
        $this->service = new PaymentMethodService($this->httpClient, $psr17, self::API_KEY, self::BASE_URL);
    }

    public function testListAllReturnsArrayOfPaymentMethod(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'paymentMethods' => [
                    ['paymentMethodType' => 'CARDS', 'circuit' => 'VISA', 'imageLink' => 'https://example.com/visa.svg', 'recurringSupported' => true, 'oneClickSupported' => true],
                    ['paymentMethodType' => 'CARDS', 'circuit' => 'MC', 'imageLink' => 'https://example.com/mc.svg', 'recurringSupported' => false, 'oneClickSupported' => false],
                ],
            ], JSON_THROW_ON_ERROR)))
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
            ->with($this->callback(
                fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/payment_methods') === true
                    && str_contains((string) $request->getUri(), '?') === false,
            ))
            ->willReturn(new Response(200, [], json_encode(['paymentMethods' => []], JSON_THROW_ON_ERROR)))
        ;

        $this->service->listAll();
    }

    public function testListAllReturnsEmptyArrayWhenKeyMissing(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([], JSON_THROW_ON_ERROR)))
        ;

        $this->assertSame([], $this->service->listAll());
    }

    public function testListAllThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->listAll();
    }
}
