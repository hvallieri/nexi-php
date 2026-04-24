<?php declare(strict_types=1);

namespace Hval\Nexi\Exception;

class InvalidRequestException extends NexiException
{
    /** @var array<array{code: string, description: string}> */
    private $errors;

    /**
     * @param array<array{code: string, description: string}> $errors
     */
    public function __construct(array $errors, int $httpStatus)
    {
        $this->errors = $errors;

        $messages = array_map(
            static function (array $e): string {
                return '[' . ($e['code'] ?? '?') . '] ' . ($e['description'] ?? '?');
            },
            $errors
        );

        parent::__construct(implode('; ', $messages), $httpStatus);
    }

    /**
     * @return array<array{code: string, description: string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
