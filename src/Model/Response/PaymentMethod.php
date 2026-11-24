<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class PaymentMethod implements ResponseModelInterface
{
    private ?string $methodType;
    private ?string $circuit;
    private ?string $imageLink;
    private ?bool $recurringSupported;
    private ?bool $oneClickSupported;

    public function __construct(
        ?string $methodType,
        ?string $circuit,
        ?string $imageLink,
        ?bool $recurringSupported,
        ?bool $oneClickSupported,
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
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            methodType: $data['methodType'] ?? null,
            circuit: $data['circuit'] ?? null,
            imageLink: $data['imageLink'] ?? null,
            recurringSupported: isset($data['recurringSupported']) ? (bool) $data['recurringSupported'] : null,
            oneClickSupported: isset($data['oneClickSupported']) ? (bool) $data['oneClickSupported'] : null,
        );
    }
}
