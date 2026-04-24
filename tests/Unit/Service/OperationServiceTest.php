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
            ->willReturn($this->makeSuccessResponse('REFUNDED'))
        ;

        $response = $this->service->refund(self::OPERATION_ID, new RefundRequest(500, 'EUR'));

        $this->assertInstanceOf(OperationResponse::class, $response);
        $this->assertSame('REFUNDED', $response->getOperationResult());
        $this->assertTrue($response->isSuccessful());
    }

    public function testRefundSendsCorrectBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $data = json_decode((string) $request->getBody(), true);

                return $data['amount'] === 500
                    && $data['currency'] === 'EUR'
                    && $data['description'] === 'Reso';
            }))
            ->willReturn($this->makeSuccessResponse('REFUNDED'))
        ;

        $this->service->refund(self::OPERATION_ID, new RefundRequest(500, 'EUR', 'Reso'));
    }

    public function testCaptureCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/operations/' . self::OPERATION_ID . '/captures') !== false;
            }))
            ->willReturn($this->makeSuccessResponse('EXECUTED'))
        ;

        $response = $this->service->capture(self::OPERATION_ID, new CaptureRequest(3545, 'EUR'));

        $this->assertSame('EXECUTED', $response->getOperationResult());
        $this->assertTrue($response->isSuccessful());
    }

    public function testCancelCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/operations/' . self::OPERATION_ID . '/cancels') !== false;
            }))
            ->willReturn($this->makeSuccessResponse('VOIDED'))
        ;

        $response = $this->service->cancel(self::OPERATION_ID, new CancelRequest());

        $this->assertSame('VOIDED', $response->getOperationResult());
        $this->assertTrue($response->isSuccessful());
    }

    public function testRefundThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->refund(self::OPERATION_ID, new RefundRequest(500, 'EUR'));
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

        $this->service->refund(self::OPERATION_ID, new RefundRequest(500, 'EUR'));
    }

    public function testRefundThrowsApiExceptionOn500WithoutErrorsKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(500, [], '{}'))
        ;

        $this->expectException(\Hval\Nexi\Exception\ApiException::class);

        $this->service->refund(self::OPERATION_ID, new RefundRequest(500, 'EUR'));
    }

    public function testRefundThrowsInvalidRequestExceptionOn400WithoutErrorsKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(400, [], '{}'))
        ;

        $this->expectException(\Hval\Nexi\Exception\InvalidRequestException::class);

        $this->service->refund(self::OPERATION_ID, new RefundRequest(500, 'EUR'));
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
            ->willReturn($this->makeSuccessResponse('REFUNDED'))
        ;

        $this->service->refund($operationId, new RefundRequest(500, 'EUR'));
    }

    private function makeSuccessResponse(string $result): Response
    {
        return new Response(200, [], json_encode([
            'operationId' => self::OPERATION_ID,
            'operationType' => 'REFUND',
            'operationResult' => $result,
        ]));
    }
}
