<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum Exemption: string
{
    case NoPreference = 'NO_PREFERENCE';
    case ChallengeRequested = 'CHALLENGE_REQUESTED';
}
