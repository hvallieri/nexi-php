<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum OperationResult: string
{
    case Authorized = 'AUTHORIZED';
    case Executed = 'EXECUTED';
    case Declined = 'DECLINED';
    case DeniedByRisk = 'DENIED_BY_RISK';
    case ThreedsValidated = 'THREEDS_VALIDATED';
    case ThreedsFailed = 'THREEDS_FAILED';
    case Pending = 'PENDING';
    case Canceled = 'CANCELED';
    case Voided = 'VOIDED';
    case Refunded = 'REFUNDED';
    case Failed = 'FAILED';
}
