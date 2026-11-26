<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum ContractType: string
{
    case MitUnscheduled = 'MIT_UNSCHEDULED';
    case MitScheduled = 'MIT_SCHEDULED';
    case Cit = 'CIT';
}
