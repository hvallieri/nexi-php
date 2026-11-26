<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Enum\ContractType;
use Hval\Nexi\Enum\RecurrenceAction;
use Hval\Nexi\Model\Request\Recurrence;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Recurrence::class)]
class RecurrenceTest extends TestCase
{
    public function testToArrayWithActionOnly(): void
    {
        $recurrence = new Recurrence(RecurrenceAction::NoRecurring);

        $this->assertSame(['action' => 'NO_RECURRING'], $recurrence->toArray());
    }

    public function testToArrayWithAllFields(): void
    {
        $recurrence = new Recurrence(
            RecurrenceAction::ContractCreation,
            'C-9876',
            ContractType::MitScheduled,
            '2025-12-31',
            '30',
        );

        $this->assertSame([
            'action' => 'CONTRACT_CREATION',
            'contractId' => 'C-9876',
            'contractType' => 'MIT_SCHEDULED',
            'contractExpiryDate' => '2025-12-31',
            'contractFrequency' => '30',
        ], $recurrence->toArray());
    }

    public function testToArrayOmitsNullFields(): void
    {
        $recurrence = new Recurrence(RecurrenceAction::SubsequentPayment, 'C-9876');

        $result = $recurrence->toArray();

        $this->assertArrayHasKey('contractId', $result);
        $this->assertArrayNotHasKey('contractType', $result);
        $this->assertArrayNotHasKey('contractExpiryDate', $result);
        $this->assertArrayNotHasKey('contractFrequency', $result);
    }
}
