<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Model\Request\CancelRequest;
use Hval\Nexi\Model\Request\CaptureRequest;
use Hval\Nexi\Model\Request\RefundRequest;
use Hval\Nexi\Model\Response\OperationDetails;
use Hval\Nexi\Model\Response\OperationResponse;
use Psr\Http\Client\ClientExceptionInterface;

class OperationService extends AbstractService
{
    /**
     * Retrieves a list of operations, optionally filtered by time range, channel, type and custom field.
     * Date parameters must be in ISO 8601 format (e.g. 2022-01-01T13:10:00.000Z).
     * The API enforces a maximum interval of one month between fromTime and toTime.
     *
     * @see https://developer.nexi.it/en/api/get-operations
     *
     * @return array<int, OperationDetails>
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function findAll(
        ?string $fromTime = null,
        ?string $toTime = null,
        ?int $maxRecords = null,
        ?string $channel = null,
        ?string $operationType = null,
        ?string $customField = null
    ): array {
        $params = [];

        if ($fromTime !== null) {
            $params['fromTime'] = $fromTime;
        }

        if ($toTime !== null) {
            $params['toTime'] = $toTime;
        }

        if ($maxRecords !== null) {
            $params['maxRecords'] = $maxRecords;
        }

        if ($channel !== null) {
            $params['channel'] = $channel;
        }

        if ($operationType !== null) {
            $params['operationType'] = $operationType;
        }

        if ($customField !== null) {
            $params['customField'] = $customField;
        }

        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/operations', $params)
        );

        $items = isset($data['operations']) && is_array($data['operations']) ? $data['operations'] : [];

        $result = [];

        foreach ($items as $item) {
            if (is_array($item) === true) {
                $result[] = OperationDetails::fromArray($item);
            }
        }

        return $result;
    }

    /**
     * Retrieves the details of a single operation by its operationId.
     *
     * @see https://developer.nexi.it/en/api/get-operations-operationid
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function find(string $operationId): OperationDetails
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/operations/' . rawurlencode($operationId))
        );

        return OperationDetails::fromArray($data);
    }

    /**
     * Refunds a completed operation, either fully or partially.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationid-refunds
     *
     * @param string|null $idempotencyKey UUID v4 — supply your own key to make retries safe; auto-generated if omitted
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function refund(string $operationId, RefundRequest $request, ?string $idempotencyKey = null): OperationResponse
    {
        $body = json_encode($request->toArray());

        if ($body === false) {
            throw new NexiException('Failed to encode refund request body.');
        }

        $data = $this->parseResponse(
            $this->post(
                $this->baseUrl . '/operations/' . rawurlencode($operationId) . '/refunds',
                $body,
                ['Idempotency-Key' => $idempotencyKey ?? $this->generateIdempotencyKey()]
            )
        );

        return OperationResponse::fromArray($data);
    }

    /**
     * Captures a previously created pre-authorisation.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationid-captures
     *
     * @param string|null $idempotencyKey UUID v4 — supply your own key to make retries safe; auto-generated if omitted
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     */
    public function capture(string $operationId, CaptureRequest $request, ?string $idempotencyKey = null): OperationResponse
    {
        $body = json_encode($request->toArray());

        if ($body === false) {
            throw new NexiException('Failed to encode capture request body.');
        }

        $data = $this->parseResponse(
            $this->post(
                $this->baseUrl . '/operations/' . rawurlencode($operationId) . '/captures',
                $body,
                ['Idempotency-Key' => $idempotencyKey ?? $this->generateIdempotencyKey()]
            )
        );

        return OperationResponse::fromArray($data);
    }

    /**
     * Cancels a pre-authorisation that has not yet been captured.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationid-cancels
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
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
