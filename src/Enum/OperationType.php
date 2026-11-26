<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum OperationType: string
{
    case Authorization = 'AUTHORIZATION';
    case Capture = 'CAPTURE';
    case Void = 'VOID';
    case Refund = 'REFUND';
    case Cancel = 'CANCEL';
    case CardVerification = 'CARD_VERIFICATION';
    case NoShow = 'NOSHOW';
    case Incremental = 'INCREMENTAL';
    case DelayCharge = 'DELAY_CHARGE';
}
