<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Model\Request;

use Hval\Nexi\Model\Request\Recurrence;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers Recurrence
 */
class RecurrenceTest extends TestCase
{
    public function testToArrayWithActionOnly(): void
    {
        $recurrence = new Recurrence(Recurrence::ACTION_NO_RECURRING);

        $this->assertSame(['action' => 'NO_RECURRING'], $recurrence->toArray());
    }

    public function testToArrayWithAllFields(): void
    {
        $recurrence = new Recurrence(
            Recurrence::ACTION_CONTRACT_CREATION,
            'C-9876',
            Recurrence::CONTRACT_TYPE_MIT_SCHEDULED,
            '2025-12-31',
            '30'
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
        $recurrence = new Recurrence(Recurrence::ACTION_SUBSEQUENT_PAYMENT, 'C-9876');

        $result = $recurrence->toArray();

        $this->assertArrayHasKey('contractId', $result);
        $this->assertArrayNotHasKey('contractType', $result);
        $this->assertArrayNotHasKey('contractExpiryDate', $result);
        $this->assertArrayNotHasKey('contractFrequency', $result);
    }
}
