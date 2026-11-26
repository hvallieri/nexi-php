<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Enum\ActionType;
use Hval\Nexi\Enum\CaptureType;
use Hval\Nexi\Enum\ContractType;
use Hval\Nexi\Enum\Exemption;
use Hval\Nexi\Enum\RecurrenceAction;
use Hval\Nexi\Model\Request\PaymentSession;
use Hval\Nexi\Model\Request\Recurrence;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PaymentSession::class)]
class PaymentSessionTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $session = new PaymentSession(
            ActionType::Pay,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
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
            ActionType::Pay,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
            'https://example.com/webhook',
        );

        $result = $session->toArray();

        $this->assertSame('https://example.com/webhook', $result['notificationUrl']);
    }

    public function testToArrayOmitsNullOptionalFields(): void
    {
        $session = new PaymentSession(
            ActionType::Pay,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
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
        $recurrence = new Recurrence(RecurrenceAction::ContractCreation, 'C123', ContractType::MitScheduled);
        $session = new PaymentSession(
            ActionType::Pay,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
            null,
            null,
            null,
            null,
            $recurrence,
        );

        $result = $session->toArray();

        $this->assertArrayHasKey('recurrence', $result);
        $this->assertSame('CONTRACT_CREATION', $result['recurrence']['action']);
    }

    public function testToArrayWithCaptureTypeExemptionsAndPaymentService(): void
    {
        $session = new PaymentSession(
            ActionType::Preauth,
            '1000',
            'ita',
            'https://example.com/result',
            'https://example.com/cancel',
            null,
            CaptureType::Explicit,
            Exemption::ChallengeRequested,
            'PAYPAL',
        );

        $this->assertSame([
            'actionType' => 'PREAUTH',
            'amount' => '1000',
            'language' => 'ita',
            'resultUrl' => 'https://example.com/result',
            'cancelUrl' => 'https://example.com/cancel',
            'captureType' => 'EXPLICIT',
            'exemptions' => 'CHALLENGE_REQUESTED',
            'paymentService' => 'PAYPAL',
        ], $session->toArray());
    }
}
