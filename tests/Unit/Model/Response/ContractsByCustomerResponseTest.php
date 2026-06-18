<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\ContractsByCustomerResponse;
use Hval\Nexi\Model\Response\ContractSummary;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers ContractsByCustomerResponse
 * @covers ContractSummary
 */
class ContractsByCustomerResponseTest extends TestCase
{
    public function testFromArrayPopulatesAllFields(): void
    {
        $response = ContractsByCustomerResponse::fromArray([
            'customerId' => 'CUST-001',
            'contracts' => [
                [
                    'contractId' => 'CONTR-001',
                    'contractType' => 'MIT_UNSCHEDULED',
                    'contractExpiryDate' => '2025-12-31',
                    'contractFrequency' => '30',
                    'paymentMethod' => 'CARD',
                    'paymentCircuit' => 'VISA',
                    'paymentInstrumentInfo' => '************1234',
                ],
            ],
        ]);

        $this->assertSame('CUST-001', $response->getCustomerId());
        $this->assertCount(1, $response->getContracts());

        $contract = $response->getContracts()[0];
        $this->assertInstanceOf(ContractSummary::class, $contract);
        $this->assertSame('CONTR-001', $contract->getContractId());
        $this->assertSame('MIT_UNSCHEDULED', $contract->getContractType());
        $this->assertSame('2025-12-31', $contract->getContractExpiryDate());
        $this->assertSame('30', $contract->getContractFrequency());
        $this->assertSame('CARD', $contract->getPaymentMethod());
        $this->assertSame('VISA', $contract->getPaymentCircuit());
        $this->assertSame('************1234', $contract->getPaymentInstrumentInfo());
    }

    public function testFromArrayWithEmptyContractsReturnsEmptyArray(): void
    {
        $response = ContractsByCustomerResponse::fromArray([
            'customerId' => 'CUST-001',
            'contracts' => [],
        ]);

        $this->assertSame('CUST-001', $response->getCustomerId());
        $this->assertSame([], $response->getContracts());
    }

    public function testFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $response = ContractsByCustomerResponse::fromArray([]);

        $this->assertNull($response->getCustomerId());
        $this->assertSame([], $response->getContracts());
    }

    public function testContractSummaryFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $contract = ContractSummary::fromArray([]);

        $this->assertNull($contract->getContractId());
        $this->assertNull($contract->getContractType());
        $this->assertNull($contract->getContractExpiryDate());
        $this->assertNull($contract->getContractFrequency());
        $this->assertNull($contract->getPaymentMethod());
        $this->assertNull($contract->getPaymentCircuit());
        $this->assertNull($contract->getPaymentInstrumentInfo());
    }
}
