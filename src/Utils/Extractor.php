<?php declare(strict_types=1);

namespace Hval\Nexi\Utils;

use Hval\Nexi\Exception\NexiException;

/**
 * @internal
 */
final class Extractor
{
    /**
     * Decodes a JSON object into an array with string keys; returns null for invalid JSON or non-object values.
     *
     * @param string $json
     *
     * @return array<string, mixed>|null
     */
    public static function fromJson(string $json): ?array
    {
        $value = json_decode($json, true);

        if (is_array($value) === false) {
            return null;
        }

        return array_filter($value, is_string(...), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Decodes a JSON object like fromJson(), but throws when the JSON is invalid or not an object.
     *
     * @param string $json
     * @param NexiException|null $exception thrown instead of the default one
     *
     * @throws NexiException
     *
     * @return array<string, mixed>
     */
    public static function fromJsonOrFail(string $json, ?NexiException $exception = null): array
    {
        return self::fromJson($json) ?? throw ($exception ?? new NexiException('Invalid JSON object.'));
    }

    /**
     * Returns the value as a string; numbers are converted, any other type returns null.
     *
     * @param array<array-key, mixed> $data
     * @param string $key
     *
     * @return string|null
     */
    public static function string(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return match (true) {
            is_string($value) => $value,
            is_int($value), is_float($value) => (string) $value,
            default => null,
        };
    }

    /**
     * Returns the array items of a list, skipping any item that is not an array.
     *
     * @param array<array-key, mixed> $data
     * @param string $key
     *
     * @return list<array<string, mixed>>
     */
    public static function list(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        if (is_array($value) === false) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            if (is_array($item) === true) {
                $items[] = array_filter($item, is_string(...), ARRAY_FILTER_USE_KEY);
            }
        }

        return $items;
    }
}
