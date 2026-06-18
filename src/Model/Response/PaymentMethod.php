<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class PaymentMethod implements ResponseModelInterface
{
    /** @var string|null */
    private $methodType;

    /** @var string|null */
    private $circuit;

    /** @var string|null */
    private $imageLink;

    /** @var bool|null */
    private $recurringSupported;

    /** @var bool|null */
    private $oneClickSupported;

    public function __construct(
        ?string $methodType,
        ?string $circuit,
        ?string $imageLink,
        ?bool $recurringSupported,
        ?bool $oneClickSupported
    ) {
        $this->methodType = $methodType;
        $this->circuit = $circuit;
        $this->imageLink = $imageLink;
        $this->recurringSupported = $recurringSupported;
        $this->oneClickSupported = $oneClickSupported;
    }

    public function getMethodType(): ?string
    {
        return $this->methodType;
    }

    public function getCircuit(): ?string
    {
        return $this->circuit;
    }

    public function getImageLink(): ?string
    {
        return $this->imageLink;
    }

    public function isRecurringSupported(): ?bool
    {
        return $this->recurringSupported;
    }

    public function isOneClickSupported(): ?bool
    {
        return $this->oneClickSupported;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['methodType'] ?? null,
            $data['circuit'] ?? null,
            $data['imageLink'] ?? null,
            isset($data['recurringSupported']) ? (bool) $data['recurringSupported'] : null,
            isset($data['oneClickSupported']) ? (bool) $data['oneClickSupported'] : null
        );
    }
}
