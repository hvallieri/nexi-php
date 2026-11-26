<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum RecurrenceAction: string
{
    case NoRecurring = 'NO_RECURRING';
    case SubsequentPayment = 'SUBSEQUENT_PAYMENT';
    case ContractCreation = 'CONTRACT_CREATION';
    case CardSubstitution = 'CARD_SUBSTITUTION';
}
