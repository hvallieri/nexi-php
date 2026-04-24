<?php declare(strict_types=1);

namespace Hval\Nexi\Model\Request;

use Hval\Nexi\Model\RequestModelInterface;

class PaymentSession implements RequestModelInterface
{
    const ACTION_PAY = 'PAY';
    const ACTION_VERIFY = 'VERIFY';
    const ACTION_PREAUTH = 'PREAUTH';

    const CAPTURE_EXPLICIT = 'EXPLICIT';
    const CAPTURE_IMPLICIT = 'IMPLICIT';

    const EXEMPTION_NO_PREFERENCE = 'NO_PREFERENCE';
    const EXEMPTION_CHALLENGE_REQUESTED = 'CHALLENGE_REQUESTED';

    /** @var string */
    private $actionType;

    /** @var string */
    private $amount;

    /** @var string */
    private $language;

    /** @var string */
    private $resultUrl;

    /** @var string */
    private $cancelUrl;

    /** @var string|null */
    private $notificationUrl;

    /** @var string|null */
    private $captureType;

    /** @var string|null */
    private $exemptions;

    /** @var string|null */
    private $paymentService;

    /** @var Recurrence|null */
    private $recurrence;

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
        ?Recurrence $recurrence = null
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
