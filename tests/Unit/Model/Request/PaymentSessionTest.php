<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Request\Recurrence;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers PaymentSession
 */
class PaymentSessionTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $session = new PaymentSession(
            PaymentSession::ACTION_PAY,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel'
        );

        $this->assertSame([
            'actionType' => 'PAY',
            'amount' => '1000',
            'language' => 'ita',
            'resultUrl' => 'https://example.com/result',
            'cancelUrl' => 'https://example.com/cancel',
        ], $session->toArray());
    }

    public function testToArrayIncludesNotificationUrl(): void
    {
        $session = new PaymentSession(
            PaymentSession::ACTION_PAY,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
            'https://example.com/webhook'
        );

        $result = $session->toArray();

        $this->assertSame('https://example.com/webhook', $result['notificationUrl']);
    }

    public function testToArrayOmitsNullOptionalFields(): void
    {
        $session = new PaymentSession(
            PaymentSession::ACTION_PAY,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel'
        );

        $result = $session->toArray();

        $this->assertArrayNotHasKey('notificationUrl', $result);
        $this->assertArrayNotHasKey('captureType', $result);
        $this->assertArrayNotHasKey('exemptions', $result);
        $this->assertArrayNotHasKey('paymentService', $result);
        $this->assertArrayNotHasKey('recurrence', $result);
    }

    public function testToArrayIncludesRecurrence(): void
    {
        $recurrence = new Recurrence(Recurrence::ACTION_CONTRACT_CREATION, 'C123', Recurrence::CONTRACT_TYPE_MIT_SCHEDULED);
        $session = new PaymentSession(
            PaymentSession::ACTION_PAY,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
            null,
            null,
            null,
            null,
            $recurrence
        );

        $result = $session->toArray();

        $this->assertArrayHasKey('recurrence', $result);
        $this->assertSame('CONTRACT_CREATION', $result['recurrence']['action']);
    }

    public function testActionTypeConstants(): void
    {
        $this->assertSame('PAY', PaymentSession::ACTION_PAY);
        $this->assertSame('VERIFY', PaymentSession::ACTION_VERIFY);
        $this->assertSame('PREAUTH', PaymentSession::ACTION_PREAUTH);
    }
}
