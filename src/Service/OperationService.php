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
     * @param string|null $fromTime
     * @param string|null $toTime
     * @param int|null $maxRecords
     * @param string|null $channel
     * @param string|null $operationType
     * @param string|null $customField
     *
     * @throws ClientExceptionInterface
     *
     * @return array<int, OperationDetails>
     */
    public function findAll(
        ?string $fromTime = null,
        ?string $toTime = null,
        ?int $maxRecords = null,
        ?string $channel = null,
        ?string $operationType = null,
        ?string $customField = null,
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
            $this->get($this->baseUrl . '/operations', $params),
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
     * @see https://developer.nexi.it/en/api/get-operations-operationId
     *
     * @param string $operationId
     *
     * @throws NexiException
     * @throws ClientExceptionInterface
     *
     * @return OperationDetails
     */
    public function find(string $operationId): OperationDetails
    {
        $data = $this->parseResponse(
            $this->get($this->baseUrl . '/operations/' . rawurlencode($operationId)),
        );

        return OperationDetails::fromArray($data);
    }

    /**
     * Refunds a completed operation, either fully or partially.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationId-refunds
     *
     * @param string $operationId
     * @param RefundRequest $request
     * @param string|null $idempotencyKey UUID v4 — supply your own key to make retries safe; auto-generated if omitted
     *
     * @throws ClientExceptionInterface
     *
     * @return OperationResponse
     */
    public function refund(string $operationId, RefundRequest $request, ?string $idempotencyKey = null): OperationResponse
    {
        $body = $this->encodeBody($request->toArray(), 'Failed to encode refund request body.', JSON_FORCE_OBJECT);

        $data = $this->parseResponse(
            $this->post(
                $this->baseUrl . '/operations/' . rawurlencode($operationId) . '/refunds',
                $body,
                ['Idempotency-Key' => $idempotencyKey ?? $this->generateIdempotencyKey()],
            ),
        );

        return OperationResponse::fromArray($data);
    }

    /**
     * Captures a previously created pre-authorisation.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationId-captures
     *
     * @param string $operationId
     * @param CaptureRequest $request
     * @param string|null $idempotencyKey UUID v4 — supply your own key to make retries safe; auto-generated if omitted
     *
     * @throws ClientExceptionInterface
     *
     * @return OperationResponse
     */
    public function capture(string $operationId, CaptureRequest $request, ?string $idempotencyKey = null): OperationResponse
    {
        $body = $this->encodeBody($request->toArray(), 'Failed to encode capture request body.', JSON_FORCE_OBJECT);

        $data = $this->parseResponse(
            $this->post(
                $this->baseUrl . '/operations/' . rawurlencode($operationId) . '/captures',
                $body,
                ['Idempotency-Key' => $idempotencyKey ?? $this->generateIdempotencyKey()],
            ),
        );

        return OperationResponse::fromArray($data);
    }

    /**
     * Cancels a pre-authorisation that has not yet been captured.
     *
     * @see https://developer.nexi.it/en/api/post-operations-operationId-cancels
     *
     * @param string $operationId
     * @param CancelRequest $request
     *
     * @throws ClientExceptionInterface
     *
     * @return OperationResponse
     */
    public function cancel(string $operationId, CancelRequest $request): OperationResponse
    {
        $body = $this->encodeBody($request->toArray(), 'Failed to encode cancel request body.', JSON_FORCE_OBJECT);

        $data = $this->parseResponse(
            $this->post($this->baseUrl . '/operations/' . rawurlencode($operationId) . '/cancels', $body),
        );

        return OperationResponse::fromArray($data);
    }
}
