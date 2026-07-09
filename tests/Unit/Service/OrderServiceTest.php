<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Exception\InvalidRequestException;
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Model\Request\Order;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Response\HppResponse;
use Hval\Nexi\Model\Response\OperationDetails;
use Hval\Nexi\Model\Response\OrderResponse;
use Hval\Nexi\Model\Response\OrderSummary;
use Hval\Nexi\Service\OrderService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @covers OrderService
 */
class OrderServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var OrderService */
    private $service;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->service = new OrderService($this->httpClient, new HttpFactory($psr17, $psr17), self::API_KEY, self::BASE_URL);
    }

    private function makeMitSuccessResponse(): Response
    {
        return new Response(200, [], json_encode([
            'operation' => [
                'orderId' => 'ORD-001',
                'operationId' => 'OP-001',
                'channel' => 'ECOMMERCE',
                'channelDetail' => 'MERCHANT_INITIATED_TRANSACTION',
                'operationType' => 'AUTHORIZATION',
                'operationResult' => 'EXECUTED',
                'operationAmount' => '1000',
                'operationCurrency' => 'EUR',
                'additionalData' => ['authorizationCode' => 'ABC123'],
            ],
        ]));
    }

    public function testCreateMitReturnsOperationDetails(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->makeMitSuccessResponse())
        ;

        $result = $this->service->createMit(new Order('ORD-001', '1000', 'EUR'), 'CONTRACT-001');

        $this->assertInstanceOf(OperationDetails::class, $result);
        $this->assertSame('ORD-001', $result->getOrderId());
        $this->assertSame('OP-001', $result->getOperationId());
        $this->assertSame('MERCHANT_INITIATED_TRANSACTION', $result->getChannelDetail());
        $this->assertSame('EXECUTED', $result->getOperationResult());
        $this->assertSame('ABC123', $result->getAdditionalData()['authorizationCode']);
    }

    public function testCreateMitCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/orders/mit') !== false
                    && $request->getMethod() === 'POST';
            }))
            ->willReturn($this->makeMitSuccessResponse())
        ;

        $this->service->createMit(new Order('ORD-001', '1000', 'EUR'), 'CONTRACT-001');
    }

    public function testCreateMitSendsOrderAndContractIdInBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return isset($data['order'])
                    && $data['order']['orderId'] === 'ORD-001'
                    && $data['contractId'] === 'CONTRACT-001'
                    && isset($data['captureType']) === false;
            }))
            ->willReturn($this->makeMitSuccessResponse())
        ;

        $this->service->createMit(new Order('ORD-001', '1000', 'EUR'), 'CONTRACT-001');
    }

    public function testCreateMitSendsCaptureTypeWhenProvided(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return $data['captureType'] === 'EXPLICIT';
            }))
            ->willReturn($this->makeMitSuccessResponse())
        ;

        $this->service->createMit(
            new Order('ORD-001', '1000', 'EUR'),
            'CONTRACT-001',
            PaymentSession::CAPTURE_EXPLICIT
        );
    }

    public function testCreateMitSendsGeneratedIdempotencyKeyHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return $request->getHeaderLine('Idempotency-Key') !== '';
            }))
            ->willReturn($this->makeMitSuccessResponse())
        ;

        $this->service->createMit(new Order('ORD-001', '1000', 'EUR'), 'CONTRACT-001');
    }

    public function testCreateMitSendsCustomIdempotencyKeyHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return $request->getHeaderLine('Idempotency-Key') === 'my-custom-key';
            }))
            ->willReturn($this->makeMitSuccessResponse())
        ;

        $this->service->createMit(new Order('ORD-001', '1000', 'EUR'), 'CONTRACT-001', null, 'my-custom-key');
    }

    public function testCreateMitThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->createMit(new Order('ORD-001', '1000', 'EUR'), 'CONTRACT-001');
    }

    public function testCreateHppReturnsHppResponse(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'hostedPage' => 'https://gateway.example.com/pay/abc123',
                'securityToken' => 'tok_abc123',
            ])))
        ;

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(
            PaymentSession::ACTION_PAY,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel'
        );

        $response = $this->service->createHpp($order, $session);

        $this->assertInstanceOf(HppResponse::class, $response);
        $this->assertSame('https://gateway.example.com/pay/abc123', $response->getHostedPage());
        $this->assertSame('tok_abc123', $response->getSecurityToken());
    }

    public function testCreateHppSendsApiKeyHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return $request->getHeaderLine('X-Api-Key') === self::API_KEY
                    && $request->hasHeader('Correlation-Id')
                    && $request->getHeaderLine('Content-Type') === 'application/json';
            }))
            ->willReturn(new Response(200, [], json_encode([
                'hostedPage' => 'https://x.com', 'securityToken' => 'tok',
            ])))
        ;

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        $this->service->createHpp($order, $session);
    }

    public function testCreateHppSendsCorrectJsonBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return isset($data['order'])
                    && isset($data['paymentSession'])
                    && $data['order']['orderId'] === 'ORD-001'
                    && $data['order']['amount'] === '1000'
                    && $data['paymentSession']['actionType'] === 'PAY';
            }))
            ->willReturn(new Response(200, [], json_encode([
                'hostedPage' => 'https://x.com', 'securityToken' => 'tok',
            ])))
        ;

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        $this->service->createHpp($order, $session);
    }

    public function testCreateHppThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        $this->service->createHpp($order, $session);
    }

    public function testCreateHppThrowsInvalidRequestExceptionOn400(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(400, [], json_encode([
                'errors' => [['code' => 'GW0001', 'description' => 'Invalid merchant URL']],
            ])))
        ;

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        $this->service->createHpp($order, $session);
    }

    public function testInvalidRequestExceptionContainsErrors(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(400, [], json_encode([
                'errors' => [['code' => 'GW0001', 'description' => 'Invalid merchant URL']],
            ])))
        ;

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        try {
            $this->service->createHpp($order, $session);
            $this->fail('Expected InvalidRequestException not thrown.');
        } catch (InvalidRequestException $e) {
            $errors = $e->getErrors();
            $this->assertCount(1, $errors);
            $this->assertSame('GW0001', $errors[0]['code']);
        }
    }

    public function testFindCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/orders/ORD-001') !== false
                    && $request->getHeaderLine('X-Api-Key') === self::API_KEY;
            }))
            ->willReturn(new Response(200, [], json_encode([
                'orderStatus' => [
                    'order' => [
                        'orderId' => 'ORD-001',
                    ],
                    'authorizedAmount' => '1000',
                    'capturedAmount' => '0',
                    'lastOperationType' => 'AUTHORIZATION',
                    'lastOperationTime' => '2024-01-01T12:00:00.000Z',
                    'warnings' => [],
                ],
                'operations' => [
                    [
                        'operationId' => 'OP-001',
                        'operationType' => 'AUTHORIZATION',
                        'operationResult' => 'AUTHORIZED',
                        'operationTime' => '2024-01-01T12:00:00.000Z',
                    ],
                ],
            ])))
        ;

        $response = $this->service->find('ORD-001');

        $this->assertInstanceOf(OrderResponse::class, $response);
        $this->assertSame('ORD-001', $response->getOrderId());
        $this->assertTrue($response->isAuthorized());
    }

    public function testCreateHppThrowsApiExceptionOn500WithoutErrorsKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], '{}'))
        ;

        $this->expectException(\Hval\Nexi\Exception\ApiException::class);

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        $this->service->createHpp($order, $session);
    }

    public function testCreateHppThrowsInvalidRequestExceptionOn400WithoutErrorsKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(400, [], '{}'))
        ;

        $this->expectException(\Hval\Nexi\Exception\InvalidRequestException::class);

        $order = new Order('ORD-001', '1000', 'EUR');
        $session = new PaymentSession(PaymentSession::ACTION_PAY, '1000', 'ita', 'https://r.com', 'https://c.com');

        $this->service->createHpp($order, $session);
    }

    public function testFindThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->find('ORD-001');
    }

    public function testFindThrowsApiExceptionOn500(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], json_encode([
                'errors' => [['code' => 'GW0001', 'description' => 'Internal server error']],
            ])))
        ;

        $this->expectException(\Hval\Nexi\Exception\ApiException::class);
        $this->expectExceptionCode(500);

        $this->service->find('ORD-001');
    }

    public function testFindAllReturnsArrayOfOrderSummary(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'orders' => [
                    ['orderId' => 'ORD-001', 'amount' => '1000', 'currency' => 'EUR'],
                    ['orderId' => 'ORD-002', 'amount' => '2000', 'currency' => 'EUR'],
                ],
            ])))
        ;

        $result = $this->service->findAll();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(OrderSummary::class, $result[0]);
        $this->assertSame('ORD-001', $result[0]->getOrderId());
        $this->assertSame('ORD-002', $result[1]->getOrderId());
    }

    public function testFindAllWithFiltersBuildsQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return strpos($uri, 'fromTime=2024-01-01') !== false
                    && strpos($uri, 'toTime=2024-01-31') !== false
                    && strpos($uri, 'maxRecords=10') !== false
                    && strpos($uri, 'customField=promo') !== false;
            }))
            ->willReturn(new Response(200, [], json_encode([])))
        ;

        $this->service->findAll('2024-01-01', '2024-01-31', 10, 'promo');
    }

    public function testFindAllOmitsNullParams(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return strpos($uri, 'maxRecords=5') !== false
                    && strpos($uri, 'fromTime') === false
                    && strpos($uri, 'toTime') === false
                    && strpos($uri, 'customField') === false;
            }))
            ->willReturn(new Response(200, [], json_encode([])))
        ;

        $this->service->findAll(null, null, 5, null);
    }

    public function testFindAllWithAmountFiltersBuildsQueryString(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return strpos($uri, 'orderId=ORD-001') !== false
                    && strpos($uri, 'amountType=AUTHORIZED_AMOUNT') !== false
                    && strpos($uri, 'minAmount=500') !== false
                    && strpos($uri, 'maxAmount=2000') !== false
                    && strpos($uri, 'orderState=TO_CAPTURE') !== false;
            }))
            ->willReturn(new Response(200, [], json_encode([])))
        ;

        $this->service->findAll(
            null,
            null,
            null,
            null,
            'ORD-001',
            OrderService::AMOUNT_TYPE_AUTHORIZED_AMOUNT,
            '500',
            '2000',
            OrderService::ORDER_STATE_TO_CAPTURE
        );
    }

    public function testFindAllOmitsNullAmountFilters(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();

                return strpos($uri, 'orderId') === false
                    && strpos($uri, 'amountType') === false
                    && strpos($uri, 'minAmount') === false
                    && strpos($uri, 'maxAmount') === false
                    && strpos($uri, 'orderState') === false;
            }))
            ->willReturn(new Response(200, [], json_encode([])))
        ;

        $this->service->findAll('2024-01-01', '2024-01-31');
    }

    public function testConstants(): void
    {
        $this->assertSame('ORDER_AMOUNT', OrderService::AMOUNT_TYPE_ORDER_AMOUNT);
        $this->assertSame('AUTHORIZED_AMOUNT', OrderService::AMOUNT_TYPE_AUTHORIZED_AMOUNT);
        $this->assertSame('CAPTURED_AMOUNT', OrderService::AMOUNT_TYPE_CAPTURED_AMOUNT);
        $this->assertSame('TO_CAPTURE', OrderService::ORDER_STATE_TO_CAPTURE);
        $this->assertSame('CAPTURED', OrderService::ORDER_STATE_CAPTURED);
    }

    public function testFindAllReturnsEmptyArrayWhenOrdersKeyMissing(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([])))
        ;

        $this->assertSame([], $this->service->findAll());
    }

    public function testFindAllThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->findAll();
    }

    public function testFindUrlEncodesOrderId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/orders/ORD%2F001') !== false;
            }))
            ->willReturn(new Response(200, [], json_encode([
                'orderStatus' => ['order' => ['orderId' => 'ORD/001']],
                'operations' => [],
            ])))
        ;

        $this->service->find('ORD/001');
    }
}
