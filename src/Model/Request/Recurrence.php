<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Enum\ContractType;
use Hval\Nexi\Enum\RecurrenceAction;
use Hval\Nexi\Model\RequestModelInterface;

class Recurrence implements RequestModelInterface
{
    private RecurrenceAction $action;
    private ?string $contractId;
    private ?ContractType $contractType;
    private ?string $contractExpiryDate;
    private ?string $contractFrequency;

    public function __construct(
        RecurrenceAction $action,
        ?string $contractId = null,
        ?ContractType $contractType = null,
        ?string $contractExpiryDate = null,
        ?string $contractFrequency = null,
    ) {
        $this->action = $action;
        $this->contractId = $contractId;
        $this->contractType = $contractType;
        $this->contractExpiryDate = $contractExpiryDate;
        $this->contractFrequency = $contractFrequency;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $data = ['action' => $this->action->value];

        if ($this->contractId !== null) {
            $data['contractId'] = $this->contractId;
        }

        if ($this->contractType !== null) {
            $data['contractType'] = $this->contractType->value;
        }

        if ($this->contractExpiryDate !== null) {
            $data['contractExpiryDate'] = $this->contractExpiryDate;
        }

        if ($this->contractFrequency !== null) {
            $data['contractFrequency'] = $this->contractFrequency;
        }

        return $data;
    }
}
