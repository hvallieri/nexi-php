<?php declare(strict_types=1);

namespace Hval\Nexi\Enum;

enum Environment: string
{
    case Production = 'production';
    case Sandbox = 'sandbox';

    public function baseUrl(): string
    {
        return match ($this) {
            self::Production => 'https://xpay.nexigroup.com/api/phoenix-0.0/psp/api/v1',
            self::Sandbox => 'https://xpaysandbox.nexigroup.com/api/phoenix-0.0/psp/api/v1',
        };
    }
}
