<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Service;

use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Model\Response\ContractsByCustomerResponse;
use Hval\Nexi\Service\ContractService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @covers ContractService
 */
class ContractServiceTest extends TestCase
{
    private const BASE_URL = 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1';
    private const API_KEY = 'test-api-key-uuid';

    /** @var ClientInterface&MockObject */
    private $httpClient;

    /** @var ContractService */
    private $service;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->service = new ContractService($this->httpClient, new HttpFactory($psr17, $psr17), self::API_KEY, self::BASE_URL);
    }

    public function testFindByCustomerReturnsContractsByCustomerResponse(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'customerId' => 'CUST-001',
                'contracts' => [
                    ['contractId' => 'CONTR-001', 'contractType' => 'MIT_UNSCHEDULED'],
                    ['contractId' => 'CONTR-002', 'contractType' => 'MIT_SCHEDULED'],
                ],
            ])))
        ;

        $response = $this->service->findByCustomer('CUST-001');

        $this->assertInstanceOf(ContractsByCustomerResponse::class, $response);
        $this->assertSame('CUST-001', $response->getCustomerId());
        $this->assertCount(2, $response->getContracts());
        $this->assertSame('CONTR-001', $response->getContracts()[0]->getContractId());
    }

    public function testFindByCustomerCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/contracts/customers/CUST-001') !== false
                    && $request->getMethod() === 'GET';
            }))
            ->willReturn(new Response(200, [], json_encode(['customerId' => 'CUST-001', 'contracts' => []])))
        ;

        $this->service->findByCustomer('CUST-001');
    }

    public function testFindByCustomerUrlEncodesCustomerId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/contracts/customers/CUST%2F001') !== false;
            }))
            ->willReturn(new Response(200, [], json_encode(['customerId' => 'CUST/001', 'contracts' => []])))
        ;

        $this->service->findByCustomer('CUST/001');
    }

    public function testFindByCustomerThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->findByCustomer('CUST-001');
    }

    public function testDeactivateCallsCorrectEndpoint(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/contracts/CONTR-001/deactivation') !== false
                    && $request->getMethod() === 'POST';
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->deactivate('CONTR-001');
    }

    public function testDeactivateUrlEncodesContractId(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                return strpos((string) $request->getUri(), '/contracts/CONTR%2F001/deactivation') !== false;
            }))
            ->willReturn(new Response(200, [], '{}'))
        ;

        $this->service->deactivate('CONTR/001');
    }

    public function testDeactivateThrowsAuthenticationExceptionOn401(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn(new Response(401))
        ;

        $this->expectException(AuthenticationException::class);

        $this->service->deactivate('CONTR-001');
    }
}
