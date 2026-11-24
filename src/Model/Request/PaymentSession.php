<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class PaymentSession implements RequestModelInterface
{
    public const string ACTION_PAY = 'PAY';
    public const string ACTION_VERIFY = 'VERIFY';
    public const string ACTION_PREAUTH = 'PREAUTH';

    public const string CAPTURE_EXPLICIT = 'EXPLICIT';
    public const string CAPTURE_IMPLICIT = 'IMPLICIT';

    public const string EXEMPTION_NO_PREFERENCE = 'NO_PREFERENCE';
    public const string EXEMPTION_CHALLENGE_REQUESTED = 'CHALLENGE_REQUESTED';

    private string $actionType;
    private string $amount;
    private string $language;
    private string $resultUrl;
    private string $cancelUrl;
    private ?string $notificationUrl;
    private ?string $captureType;
    private ?string $exemptions;
    private ?string $paymentService;
    private ?Recurrence $recurrence;

    public function __construct(
        string $actionType,
        string $amount,
        string $language,
        string $resultUrl,
        string $cancelUrl,
        ?string $notificationUrl = null,
        ?string $captureType = null,
        ?string $exemptions = null,
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
            'actionType' => $this->actionType,
            'amount' => $this->amount,
            'language' => $this->language,
            'resultUrl' => $this->resultUrl,
            'cancelUrl' => $this->cancelUrl,
        ];

        if ($this->notificationUrl !== null) {
            $data['notificationUrl'] = $this->notificationUrl;
        }

        if ($this->captureType !== null) {
            $data['captureType'] = $this->captureType;
        }

        if ($this->exemptions !== null) {
            $data['exemptions'] = $this->exemptions;
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
