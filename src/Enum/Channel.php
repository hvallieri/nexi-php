<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum Channel: string
{
    case Ecommerce = 'ECOMMERCE';
    case Pos = 'POS';
    case Backoffice = 'BACKOFFICE';
}
