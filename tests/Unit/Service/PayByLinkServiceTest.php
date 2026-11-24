<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\PayByLinkResponse;
use Hval\Nexi\Model\Response\PaymentLink;
use Hval\Nexi\Service\PayByLinkService;
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
#[CoversClass(PayByLinkService::class)]
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
        $this->service = new PayByLinkService($this->httpClient, $psr17, self::API_KEY, self::BASE_URL);
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
        ], JSON_THROW_ON_ERROR));
    }

    public function testCreateReturnsPayByLinkResponse(): void
    {
        $this->httpClient
            ->expects($this->once())
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
            ->with($this->callback(
                fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/orders/paybylink') === true
                    && $request->getMethod() === 'POST',
            ))
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
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->create($this->makeOrder(), $this->makeSession(), '2024-12-31');
    }

    public function testFindAllReturnsArrayOfPaymentLink(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'paymentLinks' => [
                    ['linkId' => 'LINK-001', 'amount' => '1000', 'status' => 'ACTIVE'],
                    ['linkId' => 'LINK-002', 'amount' => '2000', 'status' => 'EXPIRED'],
                ],
            ], JSON_THROW_ON_ERROR)))
        ;

        $result = $this->service->findAll();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(PaymentLink::class, $result[0]);
        $this->assertSame('LINK-001', $result[0]->getLinkId());
        $this->assertSame('LINK-002', $result[1]->getLinkId());
        $this->assertNull($result[0]->getSecurityToken());
    }

    public function testFindAllCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/orders/paybylink') === true
                    && $request->getMethod() === 'GET',
            ))
            ->willReturn(new Response(200, [], json_encode([], JSON_THROW_ON_ERROR)))
        ;

        $this->service->findAll();
    }

    public function testFindAllWithFiltersBuildsQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return str_contains($uri, 'fromTime=2024-01-01') === true
                    && str_contains($uri, 'toTime=2024-01-31') === true
                    && str_contains($uri, 'maxRecords=10') === true
                    && str_contains($uri, 'status=ACTIVE') === true;
            }))
            ->willReturn(new Response(200, [], json_encode([], JSON_THROW_ON_ERROR)))
        ;

        $this->service->findAll('2024-01-01', '2024-01-31', 10, PaymentLink::STATUS_ACTIVE);
    }

    public function testFindAllOmitsNullParams(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return str_contains($uri, 'status=EXPIRED') === true
                    && str_contains($uri, 'fromTime') === false
                    && str_contains($uri, 'toTime') === false
                    && str_contains($uri, 'maxRecords') === false;
            }))
            ->willReturn(new Response(200, [], json_encode([], JSON_THROW_ON_ERROR)))
        ;

        $this->service->findAll(null, null, null, PaymentLink::STATUS_EXPIRED);
    }

    public function testFindAllReturnsEmptyArrayWhenPaymentLinksKeyMissing(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([], JSON_THROW_ON_ERROR)))
        ;

        $this->assertSame([], $this->service->findAll());
    }

    public function testFindAllThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->findAll();
    }

    public function testFindReturnsPayByLinkResponse(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($this->makeSuccessResponse())
        ;

        $response = $this->service->find('LINK-001');

        $this->assertInstanceOf(PayByLinkResponse::class, $response);
        $this->assertSame('LINK-001', $response->getPaymentLink()->getLinkId());
        $this->assertSame('tok_abc123', $response->getPaymentLink()->getSecurityToken());
    }

    public function testFindCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/orders/paybylink/LINK-001') === true
                    && $request->getMethod() === 'GET',
            ))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->find('LINK-001');
    }

    public function testFindUrlEncodesLinkId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/orders/paybylink/LINK%2F001') === true))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->find('LINK/001');
    }

    public function testFindThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->find('LINK-001');
    }

    public function testRenewReturnsPayByLinkResponse(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($this->makeSuccessResponse())
        ;

        $response = $this->service->renew('LINK-001', '2024-12-31');

        $this->assertInstanceOf(PayByLinkResponse::class, $response);
        $this->assertSame('LINK-001', $response->getPaymentLink()->getLinkId());
        $this->assertSame('tok_abc123', $response->getPaymentLink()->getSecurityToken());
    }

    public function testRenewCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/orders/paybylink/LINK-001/renewals') === true
                    && $request->getMethod() === 'POST',
            ))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->renew('LINK-001');
    }

    public function testRenewSendsExpirationDateInBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return isset($data['expirationDate'])
                    && $data['expirationDate'] === '2024-12-31';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->renew('LINK-001', '2024-12-31');
    }

    public function testRenewSendsEmptyObjectBodyWithoutExpirationDate(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(fn (RequestInterface $request): bool => (string) $request->getBody() === '{}'))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->renew('LINK-001');
    }

    public function testRenewUrlEncodesLinkId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/orders/paybylink/LINK%2F001/renewals') === true))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->renew('LINK/001');
    }

    public function testRenewThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->renew('LINK-001');
    }

    public function testCancelCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/paybylink/LINK-001/cancels') === true
                    && $request->getMethod() === 'POST',
            ))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->cancel('LINK-001');
    }

    public function testCancelUrlEncodesLinkId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(fn (RequestInterface $request): bool => str_contains((string) $request->getUri(), '/paybylink/LINK%2F001/cancels') === true))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->cancel('LINK/001');
    }

    public function testCancelThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->cancel('LINK-001');
    }

    public function testCreateThrowsNexiExceptionWhenBodyCannotBeEncoded(): void
    {
        $this->httpClient
            ->expects($this->never())
            ->method('sendRequest')
        ;

        $this->expectException(NexiException::class);
        $this->expectExceptionMessage('Failed to encode Pay-by-Link request body.');

        $this->service->create($this->makeOrder(), $this->makeSession(), "2026-12-31\xB1");
    }

    public function testRenewThrowsNexiExceptionWhenBodyCannotBeEncoded(): void
    {
        $this->httpClient
            ->expects($this->never())
            ->method('sendRequest')
        ;

        $this->expectException(NexiException::class);
        $this->expectExceptionMessage('Failed to encode Pay-by-Link renewal request body.');

        $this->service->renew('LINK-001', "2026-12-31\xB1");
    }
}
