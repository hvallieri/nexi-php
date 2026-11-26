<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Response;

use Hval\Nexi\Model\Response\PayByLinkResponse;
use Hval\Nexi\Model\Response\PaymentLink;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PayByLinkResponse::class)]
#[CoversClass(PaymentLink::class)]
class PayByLinkResponseTest extends TestCase
{
    public function testFromArrayPopulatesPaymentLink(): void
    {
        $response = PayByLinkResponse::fromArray([
            'paymentLink' => [
                'linkId' => 'LINK-001',
                'amount' => '1000',
                'expirationDate' => '2024-12-31',
                'link' => 'https://pay.example.com/link/abc',
                'paidByOperationId' => null,
                'status' => 'ACTIVE',
                'securityToken' => 'tok_abc123',
            ],
        ]);

        $link = $response->getPaymentLink();
        $this->assertInstanceOf(PaymentLink::class, $link);
        $this->assertSame('LINK-001', $link->getLinkId());
        $this->assertSame('1000', $link->getAmount());
        $this->assertSame('2024-12-31', $link->getExpirationDate());
        $this->assertSame('https://pay.example.com/link/abc', $link->getLink());
        $this->assertNull($link->getPaidByOperationId());
        $this->assertSame('ACTIVE', $link->getStatus());
        $this->assertSame('tok_abc123', $link->getSecurityToken());
    }

    public function testFromArrayReadsSecurityTokenFromRoot(): void
    {
        $response = PayByLinkResponse::fromArray([
            'paymentLink' => [
                'linkId' => 'LINK-001',
                'status' => 'ACTIVE',
            ],
            'securityToken' => 'tok_root',
            'warnings' => [],
        ]);

        $link = $response->getPaymentLink();
        $this->assertInstanceOf(PaymentLink::class, $link);
        $this->assertSame('LINK-001', $link->getLinkId());
        $this->assertSame('tok_root', $link->getSecurityToken());
    }

    public function testFromArrayWithoutSecurityTokenReturnsNullToken(): void
    {
        $response = PayByLinkResponse::fromArray([
            'paymentLink' => ['linkId' => 'LINK-001'],
        ]);

        $link = $response->getPaymentLink();
        $this->assertInstanceOf(PaymentLink::class, $link);
        $this->assertNull($link->getSecurityToken());
    }

    public function testFromArrayWithMissingPaymentLinkReturnsNull(): void
    {
        $response = PayByLinkResponse::fromArray([]);

        $this->assertNull($response->getPaymentLink());
    }

    public function testPaymentLinkFromArrayWithMissingFieldsReturnsNulls(): void
    {
        $link = PaymentLink::fromArray([]);

        $this->assertNull($link->getLinkId());
        $this->assertNull($link->getAmount());
        $this->assertNull($link->getExpirationDate());
        $this->assertNull($link->getLink());
        $this->assertNull($link->getPaidByOperationId());
        $this->assertNull($link->getStatus());
        $this->assertNull($link->getSecurityToken());
    }
}
