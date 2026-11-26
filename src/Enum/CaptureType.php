<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum CaptureType: string
{
    case Explicit = 'EXPLICIT';
    case Implicit = 'IMPLICIT';
}
