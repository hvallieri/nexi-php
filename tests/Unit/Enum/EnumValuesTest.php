<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Enum;

use Hval\Nexi\Enum\ActionType;
use Hval\Nexi\Enum\AmountType;
use Hval\Nexi\Enum\CaptureType;
use Hval\Nexi\Enum\Channel;
use Hval\Nexi\Enum\ContractType;
use Hval\Nexi\Enum\Environment;
use Hval\Nexi\Enum\Exemption;
use Hval\Nexi\Enum\OperationResult;
use Hval\Nexi\Enum\OperationType;
use Hval\Nexi\Enum\OrderState;
use Hval\Nexi\Enum\PaymentLinkStatus;
use Hval\Nexi\Enum\RecurrenceAction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ActionType::class)]
#[CoversClass(AmountType::class)]
#[CoversClass(CaptureType::class)]
#[CoversClass(Channel::class)]
#[CoversClass(ContractType::class)]
#[CoversClass(Environment::class)]
#[CoversClass(Exemption::class)]
#[CoversClass(OperationResult::class)]
#[CoversClass(OperationType::class)]
#[CoversClass(OrderState::class)]
#[CoversClass(PaymentLinkStatus::class)]
#[CoversClass(RecurrenceAction::class)]
class EnumValuesTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string<\BackedEnum>, list<string>}>
     */
    public static function enumProvider(): iterable
    {
        yield 'Environment' => [Environment::class, ['production', 'sandbox']];
        yield 'ActionType' => [ActionType::class, ['PAY', 'VERIFY', 'PREAUTH']];
        yield 'CaptureType' => [CaptureType::class, ['EXPLICIT', 'IMPLICIT']];
        yield 'Exemption' => [Exemption::class, ['NO_PREFERENCE', 'CHALLENGE_REQUESTED']];
        yield 'RecurrenceAction' => [RecurrenceAction::class, ['NO_RECURRING', 'SUBSEQUENT_PAYMENT', 'CONTRACT_CREATION', 'CARD_SUBSTITUTION']];
        yield 'ContractType' => [ContractType::class, ['MIT_UNSCHEDULED', 'MIT_SCHEDULED', 'CIT']];
        yield 'AmountType' => [AmountType::class, ['ORDER_AMOUNT', 'AUTHORIZED_AMOUNT', 'CAPTURED_AMOUNT']];
        yield 'OrderState' => [OrderState::class, ['TO_CAPTURE', 'CAPTURED']];
        yield 'Channel' => [Channel::class, ['ECOMMERCE', 'POS', 'BACKOFFICE']];
        yield 'OperationType' => [OperationType::class, ['AUTHORIZATION', 'CAPTURE', 'VOID', 'REFUND', 'CANCEL', 'CARD_VERIFICATION', 'NOSHOW', 'INCREMENTAL', 'DELAY_CHARGE']];
        yield 'OperationResult' => [OperationResult::class, ['AUTHORIZED', 'EXECUTED', 'DECLINED', 'DENIED_BY_RISK', 'THREEDS_VALIDATED', 'THREEDS_FAILED', 'PENDING', 'CANCELED', 'VOIDED', 'REFUNDED', 'FAILED']];
        yield 'PaymentLinkStatus' => [PaymentLinkStatus::class, ['ACTIVE', 'DELETED', 'EXPIRED', 'INACTIVE']];
    }

    /**
     * @param class-string<\BackedEnum> $enum
     * @param list<string> $expected
     *
     * @return void
     */
    #[DataProvider('enumProvider')]
    public function testValuesMatchNexiApi(string $enum, array $expected): void
    {
        $this->assertSame($expected, array_map(fn (\BackedEnum $case): int|string => $case->value, $enum::cases()));
    }
}
