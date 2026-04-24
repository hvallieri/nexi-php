<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\CancelRequest;
use Hval\Nexi\Model\Request\CaptureRequest;
use Hval\Nexi\Model\Request\RefundRequest;
use Hval\Nexi\Model\Response\OperationResponse;
use Psr\Http\Client\ClientExceptionInterface;

class OperationService extends AbstractService
{
    /**
     * Refunds a completed operation, either fully or partially.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationid-refunds
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function refund(string $operationId, RefundRequest $request): OperationResponse
    {
        $body = json_encode($request->toArray());

        if ($body === false) {
            throw new NexiException('Failed to encode refund request body.');
        }

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/operations/' . rawurlencode($operationId) . '/refunds', $body)
        );

        return OperationResponse::fromArray($data);
    }

    /**
     * Captures a previously created pre-authorisation.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationid-captures
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function capture(string $operationId, CaptureRequest $request): OperationResponse
    {
        $body = json_encode($request->toArray());

        if ($body === false) {
            throw new NexiException('Failed to encode capture request body.');
        }

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/operations/' . rawurlencode($operationId) . '/captures', $body)
        );

        return OperationResponse::fromArray($data);
    }

    /**
     * Cancels a pre-authorisation that has not yet been captured.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationid-cancels
     *
     * @throws NexiException
     */
    public function cancel(string $operationId, CancelRequest $request): OperationResponse
    {
        $body = json_encode($request->toArray());

        if ($body === false) {
            throw new NexiException('Failed to encode cancel request body.');
        }

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/operations/' . rawurlencode($operationId) . '/cancels', $body)
        );

        return OperationResponse::fromArray($data);
    }
}
