<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Response;

use Hval\Nexi\Model\ResponseModelInterface;

class OrderResponse implements ResponseModelInterface
{
    const ORDER_STATUS_PENDING = 'PENDING';
    const ORDER_STATUS_AUTHORIZED = 'AUTHORIZED';
    const ORDER_STATUS_EXECUTED = 'EXECUTED';
    const ORDER_STATUS_DECLINED = 'DECLINED';
    const ORDER_STATUS_CANCELED = 'CANCELED';

    /** @var string */
    private $orderId;

    /** @var string */
    private $status;

    /** @var array<string, mixed> */
    private $raw;

    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(string $orderId, string $status, array $raw)
    {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->raw = $raw;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isAuthorized(): bool
    {
        return $this->status === self::ORDER_STATUS_AUTHORIZED;
    }

    public function isExecuted(): bool
    {
        return $this->status === self::ORDER_STATUS_EXECUTED;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['orderId'] ?? ''),
            (string) ($data['orderStatus'] ?? ''),
            $data
        );
    }
}
