<?php declare(strict_types=1);

namespace Hval\Nexi\Exception;

class ApiException extends NexiException
{
    /** @var array<array{code?: string, description?: string}> */
    private array $errors;

    /**
     * @param array<array{code?: string, description?: string}> $errors
     * @param int $httpStatus
     */
    public function __construct(array $errors, int $httpStatus)
    {
        $this->errors = $errors;

        $messages = array_map(
            static fn (array $e): string => '[' . ($e['code'] ?? '?') . '] ' . ($e['description'] ?? '?'),
            $errors,
        );

        parent::__construct(implode('; ', $messages), $httpStatus);
    }

    /**
     * @return array<array{code?: string, description?: string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
