<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\PayByLinkResponse;
use Hval\Nexi\Service\PayByLinkService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @covers PayByLinkService
 */
class PayByLinkServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var PayByLinkService */
    private $service;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->service = new PayByLinkService($this->httpClient, new HttpFactory($psr17, $psr17), self::API_KEY, self::BASE_URL);
    }

    private function makeOrder(): Order
    {
        return new Order('ORD-001', '1000', 'EUR');
    }

    private function makeSession(): PaymentSession
    {
        return new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');
    }

    private function makeSuccessResponse(): Response
    {
        return new Response(200, [], json_encode([
            'paymentLink' => [
                'linkId' => 'LINK-001',
                'amount' => '1000',
                'expirationDate' => '2024-12-31',
                'link' => 'https://pay.example.com/link/abc',
                'status' => 'ACTIVE',
                'securityToken' => 'tok_abc123',
            ],
        ]));
    }

    public function testCreateReturnsPayByLinkResponse(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->makeSuccessResponse())
        ;

        $response = $this->service->create($this->makeOrder(), $this->makeSession(), '2024-12-31');

        $this->assertInstanceOf(PayByLinkResponse::class, $response);
        $this->assertSame('LINK-001', $response->getPaymentLink()->getLinkId());
        $this->assertSame('tok_abc123', $response->getPaymentLink()->getSecurityToken());
    }

    public function testCreateCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/orders/paybylink') !== false
                    && $request->getMethod() === 'POST';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->create($this->makeOrder(), $this->makeSession(), '2024-12-31');
    }

    public function testCreateInjectsExpirationDateIntoSession(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return isset($data['paymentSession']['expirationDate'])
                    && $data['paymentSession']['expirationDate'] === '2024-12-31';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->create($this->makeOrder(), $this->makeSession(), '2024-12-31');
    }

    public function testCreateThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->create($this->makeOrder(), $this->makeSession(), '2024-12-31');
    }

    public function testCancelCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/paybylink/LINK-001/cancels') !== false
                    && $request->getMethod() === 'POST';
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->cancel('LINK-001');
    }

    public function testCancelUrlEncodesLinkId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/paybylink/LINK%2F001/cancels') !== false;
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->cancel('LINK/001');
    }

    public function testCancelThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->cancel('LINK-001');
    }
}
