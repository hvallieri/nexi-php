<?php declare(strict_types=1);

namespace Hval\Nexi\Http;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

interface HttpFactoryInterface extends RequestFactoryInterface, StreamFactoryInterface
{
}
