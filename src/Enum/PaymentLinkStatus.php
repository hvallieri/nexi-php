<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum PaymentLinkStatus: string
{
    case Active = 'ACTIVE';
    case Deleted = 'DELETED';
    case Expired = 'EXPIRED';
    case Inactive = 'INACTIVE';
}
