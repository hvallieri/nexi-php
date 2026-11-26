<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum AmountType: string
{
    case OrderAmount = 'ORDER_AMOUNT';
    case AuthorizedAmount = 'AUTHORIZED_AMOUNT';
    case CapturedAmount = 'CAPTURED_AMOUNT';
}
