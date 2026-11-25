<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\ApiException;
use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Exception\InvalidRequestException;
use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Utils\Extractor;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class AbstractService
{
    public function __construct(
        protected readonly ClientInterface $httpClient,
        protected readonly RequestFactoryInterface&StreamFactoryInterface $factory,
        protected readonly string $apiKey,
        protected readonly string $baseUrl,
    ) {
    }

    /**
     * @return array<string, string>
     */
    protected function buildHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'X-Api-Key' => $this->apiKey,
            'Correlation-Id' => $this->generateCorrelationId(),
        ];
    }

    protected function generateCorrelationId(): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(random_bytes(4)),
            bin2hex(random_bytes(2)),
            bin2hex(random_bytes(2)),
            bin2hex(random_bytes(2)),
            bin2hex(random_bytes(6)),
        );
    }

    protected function generateIdempotencyKey(): string
    {
        return $this->generateCorrelationId();
    }

    /**
     * @param array<string, mixed> $data
     * @param string $errorMessage
     * @param int $flags additional json_encode() flags, e.g. JSON_FORCE_OBJECT to send {} for an empty body
     *
     * @throws NexiException
     *
     * @return string
     */
    protected function encodeBody(array $data, string $errorMessage, int $flags = 0): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR | $flags);
        } catch (\JsonException $e) {
            throw new NexiException($errorMessage, 0, $e);
        }
    }

    /**
     * @param string $url
     * @param string $body
     * @param array<string, string> $extraHeaders
     *
     * @throws ClientExceptionInterface
     *
     * @return array{status: int, body: string}
     */
    protected function post(string $url, string $body, array $extraHeaders = []): array
    {
        $request = $this->factory
            ->createRequest('POST', $url)
            ->withBody($this->factory->createStream($body))
        ;

        foreach (array_merge($this->buildHeaders(), $extraHeaders) as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        $response = $this->httpClient->sendRequest($request);

        return [
            'status' => $response->getStatusCode(),
            'body' => (string) $response->getBody(),
        ];
    }

    /**
     * @param string $url
     * @param array<string, mixed> $queryParams
     *
     * @throws ClientExceptionInterface
     *
     * @return array{status: int, body: string}
     */
    protected function get(string $url, array $queryParams = []): array
    {
        if (empty($queryParams) === false) {
            $url .= '?' . http_build_query($queryParams);
        }

        $request = $this->factory->createRequest('GET', $url);

        foreach ($this->buildHeaders() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        $response = $this->httpClient->sendRequest($request);

        return [
            'status' => $response->getStatusCode(),
            'body' => (string) $response->getBody(),
        ];
    }

    /**
     * @param array{status: int, body: string} $response
     *
     * @return array<string, mixed>
     */
    protected function parseResponse(array $response): array
    {
        $status = $response['status'];
        $data = Extractor::fromJson($response['body']) ?? [];

        return match (true) {
            $status === 401 => throw new AuthenticationException('Invalid API key or unauthorized.', 401),
            $status === 400 => throw new InvalidRequestException($this->extractErrors($data), $status),
            $status >= 400 => throw new ApiException($this->extractErrors($data), $status),
            default => $data,
        };
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<array{code?: string, description?: string}>
     */
    private function extractErrors(array $data): array
    {
        $errors = [];

        foreach (Extractor::list($data, 'errors') as $item) {
            $error = [];
            $code = Extractor::string($item, 'code');
            $description = Extractor::string($item, 'description');

            if ($code !== null) {
                $error['code'] = $code;
            }

            if ($description !== null) {
                $error['description'] = $description;
            }

            $errors[] = $error;
        }

        return $errors;
    }
}
