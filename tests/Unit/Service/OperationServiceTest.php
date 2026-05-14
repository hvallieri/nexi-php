<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\ApiException;
use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Model\Request\CancelRequest;
use Hval\Nexi\Model\Request\CaptureRequest;
use Hval\Nexi\Model\Request\RefundRequest;
use Hval\Nexi\Model\Response\OperationResponse;
use Hval\Nexi\Service\OperationService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @covers OperationService
 */
class OperationServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';
    private const OPERATION_ID = 'OP-12345';

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var OperationService */
    private $service;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->service = new OperationService($this->httpClient, new HttpFactory($psr17, $psr17), self::API_KEY, self::BASE_URL);
    }

    public function testRefundCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/operations/' . self::OPERATION_ID . '/refunds') !== false
                    && $request->getHeaderLine('X-Api-Key') === self::API_KEY;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $response = $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'));

        $this->assertInstanceOf(OperationResponse::class, $response);
        $this->assertSame(self::OPERATION_ID, $response->getOperationId());
        $this->assertNotNull($response->getOperationTime());
    }

    public function testRefundSendsCorrectBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return $data['amount'] === '500'
                    && $data['currency'] === 'EUR'
                    && $data['description'] === 'Reso';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR', 'Reso'));
    }

    public function testCaptureCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/operations/' . self::OPERATION_ID . '/captures') !== false;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $response = $this->service->capture(self::OPERATION_ID, new CaptureRequest('3545', 'EUR'));

        $this->assertSame(self::OPERATION_ID, $response->getOperationId());
        $this->assertNotNull($response->getOperationTime());
    }

    public function testCancelCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/operations/' . self::OPERATION_ID . '/cancels') !== false;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $response = $this->service->cancel(self::OPERATION_ID, new CancelRequest());

        $this->assertSame(self::OPERATION_ID, $response->getOperationId());
        $this->assertNotNull($response->getOperationTime());
    }

    public function testCaptureSendsCorrectBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return $data['amount'] === '3545'
                    && $data['currency'] === 'EUR'
                    && $data['description'] === 'Cattura ordine';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->capture(self::OPERATION_ID, new CaptureRequest('3545', 'EUR', 'Cattura ordine'));
    }

    public function testCancelSendsCorrectBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return $data['description'] === 'Annullato dal cliente';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->cancel(self::OPERATION_ID, new CancelRequest('Annullato dal cliente'));
    }

    public function testRefundThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'));
    }

    public function testRefundThrowsApiExceptionOn500(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], json_encode([
                'errors' => [['code' => 'PS0001', 'description' => 'Internal server error']],
            ])))
        ;

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'));
    }

    public function testCaptureThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->capture(self::OPERATION_ID, new CaptureRequest('3545', 'EUR'));
    }

    public function testCaptureThrowsApiExceptionOn500(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], json_encode([
                'errors' => [['code' => 'PS0001', 'description' => 'Internal server error']],
            ])))
        ;

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);

        $this->service->capture(self::OPERATION_ID, new CaptureRequest('3545', 'EUR'));
    }

    public function testCancelThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->cancel(self::OPERATION_ID, new CancelRequest());
    }

    public function testCancelThrowsApiExceptionOn500(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], json_encode([
                'errors' => [['code' => 'PS0001', 'description' => 'Internal server error']],
            ])))
        ;

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);

        $this->service->cancel(self::OPERATION_ID, new CancelRequest());
    }

    public function testRefundThrowsApiExceptionOn500WithoutErrorsKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], '{}'))
        ;

        $this->expectException(\Hval\Nexi\Exception\ApiException::class);

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'));
    }

    public function testRefundThrowsInvalidRequestExceptionOn400WithoutErrorsKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(400, [], '{}'))
        ;

        $this->expectException(\Hval\Nexi\Exception\InvalidRequestException::class);

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'));
    }

    public function testRefundSendsIdempotencyKeyHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $key = $request->getHeaderLine('Idempotency-Key');

                return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $key) === 1;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'));
    }

    public function testRefundUsesProvidedIdempotencyKey(): void
    {
        $key = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($key): bool {
                return $request->getHeaderLine('Idempotency-Key') === $key;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->refund(self::OPERATION_ID, new RefundRequest('500', 'EUR'), $key);
    }

    public function testCaptureSendsIdempotencyKeyHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $key = $request->getHeaderLine('Idempotency-Key');

                return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $key) === 1;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->capture(self::OPERATION_ID, new CaptureRequest('3545', 'EUR'));
    }

    public function testCaptureUsesProvidedIdempotencyKey(): void
    {
        $key = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($key): bool {
                return $request->getHeaderLine('Idempotency-Key') === $key;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->capture(self::OPERATION_ID, new CaptureRequest('3545', 'EUR'), $key);
    }

    public function testCancelDoesNotSendIdempotencyKeyHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return $request->getHeaderLine('Idempotency-Key') === '';
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->cancel(self::OPERATION_ID, new CancelRequest());
    }

    public function testOperationIdIsUrlEncoded(): void
    {
        $operationId = 'OP/12 345';

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/operations/OP%2F12%20345/refunds') !== false;
            }))
            ->willReturn($this->makeSuccessResponse())
        ;

        $this->service->refund($operationId, new RefundRequest('500', 'EUR'));
    }

    private function makeSuccessResponse(): Response
    {
        return new Response(200, [], json_encode([
            'operationId' => self::OPERATION_ID,
            'operationTime' => '2024-01-01T12:00:00.001Z',
        ]));
    }
}
