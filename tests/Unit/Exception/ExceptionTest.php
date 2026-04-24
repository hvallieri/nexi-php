<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Exception;

use Hval\Nexi\Exception\ApiException;
use Hval\Nexi\Exception\InvalidRequestException;
use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Exception\WebhookSignatureException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers ApiException
 * @covers InvalidRequestException
 * @covers WebhookSignatureException
 */
class ExceptionTest extends TestCase
{
    public function testApiExceptionExtendsNexiException(): void
    {
        $e = new ApiException([], 500);

        $this->assertInstanceOf(NexiException::class, $e);
    }

    public function testApiExceptionGetErrors(): void
    {
        $errors = [
            ['code' => 'GW0001', 'description' => 'Internal error'],
            ['code' => 'GW0002', 'description' => 'Timeout'],
        ];

        $e = new ApiException($errors, 500);

        $this->assertSame($errors, $e->getErrors());
        $this->assertSame(500, $e->getCode());
    }

    public function testApiExceptionFormatsMessageFromErrors(): void
    {
        $e = new ApiException([
            ['code' => 'GW0001', 'description' => 'Internal error'],
            ['code' => 'GW0002', 'description' => 'Timeout'],
        ], 500);

        $this->assertStringContainsString('[GW0001]', $e->getMessage());
        $this->assertStringContainsString('[GW0002]', $e->getMessage());
        $this->assertStringContainsString('Internal error', $e->getMessage());
    }

    public function testApiExceptionWithEmptyErrorsHasEmptyMessage(): void
    {
        $e = new ApiException([], 500);

        $this->assertSame('', $e->getMessage());
        $this->assertSame([], $e->getErrors());
    }

    public function testApiExceptionWithMissingDescription(): void
    {
        $e = new ApiException([['code' => 'GW0001']], 500);

        $this->assertStringContainsString('[GW0001]', $e->getMessage());
        $this->assertStringContainsString('?', $e->getMessage());
    }

    public function testApiExceptionWithMissingCode(): void
    {
        $e = new ApiException([['description' => 'Internal error']], 500);

        $this->assertStringContainsString('[?]', $e->getMessage());
        $this->assertStringContainsString('Internal error', $e->getMessage());
    }

    public function testInvalidRequestExceptionExtendsNexiException(): void
    {
        $e = new InvalidRequestException([], 400);

        $this->assertInstanceOf(NexiException::class, $e);
    }

    public function testInvalidRequestExceptionGetErrors(): void
    {
        $errors = [['code' => 'GW0001', 'description' => 'Invalid merchant URL']];

        $e = new InvalidRequestException($errors, 400);

        $this->assertSame($errors, $e->getErrors());
        $this->assertSame(400, $e->getCode());
    }

    public function testInvalidRequestExceptionFormatsMessage(): void
    {
        $e = new InvalidRequestException([
            ['code' => 'GW0001', 'description' => 'Invalid merchant URL'],
        ], 400);

        $this->assertStringContainsString('[GW0001]', $e->getMessage());
        $this->assertStringContainsString('Invalid merchant URL', $e->getMessage());
    }

    public function testWebhookSignatureExceptionExtendsNexiException(): void
    {
        $e = new WebhookSignatureException('Token mismatch');

        $this->assertInstanceOf(NexiException::class, $e);
        $this->assertSame('Token mismatch', $e->getMessage());
    }
}
