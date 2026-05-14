<?php declare(strict_types=1);

namespace Hval\Nexi\Service;

use Hval\Nexi\Exception\ApiException;
use Hval\Nexi\Exception\AuthenticationException;
use Hval\Nexi\Exception\InvalidRequestException;
use Hval\Nexi\Http\HttpFactoryInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

abstract class AbstractService
{
    /** @var ClientInterface */
    protected $httpClient;

    /** @var HttpFactoryInterface */
    protected $factory;

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $baseUrl;

    public function __construct(
        ClientInterface $httpClient,
        HttpFactoryInterface $factory,
        string $apiKey,
        string $baseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->factory = $factory;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
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
            bin2hex(random_bytes(6))
        );
    }

    protected function generateIdempotencyKey(): string
    {
        return $this->generateCorrelationId();
    }

    /**
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
     * @throws ClientExceptionInterface
     *
     * @return array{status: int, body: string}
     */
    protected function get(string $url): array
    {
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
        $data = json_decode($response['body'], true);

        if (!is_array($data)) {
            $data = [];
        }

        if ($status === 401) {
            throw new AuthenticationException('Invalid API key or unauthorized.', 401);
        }

        if ($status === 400) {
            throw new InvalidRequestException((array) ($data['errors'] ?? []), $status);
        }

        if ($status >= 400) {
            throw new ApiException((array) ($data['errors'] ?? []), $status);
        }

        return $data;
    }
}
