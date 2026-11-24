<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class Recurrence implements RequestModelInterface
{
    public const string ACTION_NO_RECURRING = 'NO_RECURRING';
    public const string ACTION_SUBSEQUENT_PAYMENT = 'SUBSEQUENT_PAYMENT';
    public const string ACTION_CONTRACT_CREATION = 'CONTRACT_CREATION';
    public const string ACTION_CARD_SUBSTITUTION = 'CARD_SUBSTITUTION';

    public const string CONTRACT_TYPE_MIT_UNSCHEDULED = 'MIT_UNSCHEDULED';
    public const string CONTRACT_TYPE_MIT_SCHEDULED = 'MIT_SCHEDULED';
    public const string CONTRACT_TYPE_CIT = 'CIT';

    private string $action;
    private ?string $contractId;
    private ?string $contractType;
    private ?string $contractExpiryDate;
    private ?string $contractFrequency;

    public function __construct(
        string $action,
        ?string $contractId = null,
        ?string $contractType = null,
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
        $data = ['action' => $this->action];

        if ($this->contractId !== null) {
            $data['contractId'] = $this->contractId;
        }

        if ($this->contractType !== null) {
            $data['contractType'] = $this->contractType;
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
