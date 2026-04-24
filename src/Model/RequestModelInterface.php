<?php declare(strict_types=1);

namespace Hval\Nexi\Model;

interface RequestModelInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
