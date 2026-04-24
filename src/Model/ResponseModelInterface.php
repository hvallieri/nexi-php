<?php declare(strict_types=1);

namespace Hval\Nexi\Model;

interface ResponseModelInterface
{
    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data);
}
