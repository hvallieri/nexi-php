<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum ActionType: string
{
    case Pay = 'PAY';
    case Verify = 'VERIFY';
    case Preauth = 'PREAUTH';
}
