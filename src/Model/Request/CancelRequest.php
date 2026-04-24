<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class CancelRequest implements RequestModelInterface
{
    /** @var string|null */
    private $description;

    public function __construct(?string $description = null)
    {
        $this->description = $description;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
