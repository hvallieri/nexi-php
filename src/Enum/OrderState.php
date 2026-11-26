<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum OrderState: string
{
    case ToCapture = 'TO_CAPTURE';
    case Captured = 'CAPTURED';
}
