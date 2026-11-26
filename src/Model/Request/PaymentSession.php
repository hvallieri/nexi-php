<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Enum\ActionType;
use Hval\Nexi\Enum\CaptureType;
use Hval\Nexi\Enum\Exemption;
use Hval\Nexi\Model\RequestModelInterface;

class PaymentSession implements RequestModelInterface
{
    private ActionType $actionType;
    private string $amount;
    private string $language;
    private string $resultUrl;
    private string $cancelUrl;
    private ?string $notificationUrl;
    private ?CaptureType $captureType;
    private ?Exemption $exemptions;
    private ?string $paymentService;
    private ?Recurrence $recurrence;

    public function __construct(
        ActionType $actionType,
        string $amount,
        string $language,
        string $resultUrl,
        string $cancelUrl,
        ?string $notificationUrl = null,
        ?CaptureType $captureType = null,
        ?Exemption $exemptions = null,
        ?string $paymentService = null,
        ?Recurrence $recurrence = null,
    ) {
        $this->actionType = $actionType;
        $this->amount = $amount;
        $this->language = $language;
        $this->resultUrl = $resultUrl;
        $this->cancelUrl = $cancelUrl;
        $this->notificationUrl = $notificationUrl;
        $this->captureType = $captureType;
        $this->exemptions = $exemptions;
        $this->paymentService = $paymentService;
        $this->recurrence = $recurrence;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'actionType' => $this->actionType->value,
            'amount' => $this->amount,
            'language' => $this->language,
            'resultUrl' => $this->resultUrl,
            'cancelUrl' => $this->cancelUrl,
        ];

        if ($this->notificationUrl !== null) {
            $data['notificationUrl'] = $this->notificationUrl;
        }

        if ($this->captureType !== null) {
            $data['captureType'] = $this->captureType->value;
        }

        if ($this->exemptions !== null) {
            $data['exemptions'] = $this->exemptions->value;
        }

        if ($this->paymentService !== null) {
            $data['paymentService'] = $this->paymentService;
        }

        if ($this->recurrence !== null) {
            $data['recurrence'] = $this->recurrence->toArray();
        }

        return $data;
    }
}
