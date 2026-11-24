<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Utils;

use Hval\Nexi\Exception\NexiException;
use Hval\Nexi\Exception\WebhookSignatureException;
use Hval\Nexi\Utils\Extractor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Extractor::class)]
class ExtractorTest extends TestCase
{
    public function testFromJsonDecodesObject(): void
    {
        $this->assertSame(
            ['orderId' => 'ORD-001', 'amount' => 1000],
            Extractor::fromJson('{"orderId":"ORD-001","amount":1000}'),
        );
    }

    public function testFromJsonReturnsEmptyArrayForEmptyObject(): void
    {
        $this->assertSame([], Extractor::fromJson('{}'));
    }

    public function testFromJsonReturnsNullForInvalidJson(): void
    {
        $this->assertNull(Extractor::fromJson('not json'));
        $this->assertNull(Extractor::fromJson(''));
    }

    public function testFromJsonReturnsNullForScalar(): void
    {
        $this->assertNull(Extractor::fromJson('"text"'));
        $this->assertNull(Extractor::fromJson('123'));
    }

    public function testFromJsonDropsNumericKeys(): void
    {
        $this->assertSame([], Extractor::fromJson('["a","b"]'));
        $this->assertSame(['name' => 'x'], Extractor::fromJson('{"0":"zero","name":"x"}'));
    }

    public function testFromJsonOrFailDecodesObject(): void
    {
        $this->assertSame(['orderId' => 'ORD-001'], Extractor::fromJsonOrFail('{"orderId":"ORD-001"}'));
    }

    public function testFromJsonOrFailThrowsDefaultExceptionForInvalidJson(): void
    {
        $this->expectException(NexiException::class);
        $this->expectExceptionMessage('Invalid JSON object.');

        Extractor::fromJsonOrFail('not json');
    }

    public function testFromJsonOrFailThrowsGivenException(): void
    {
        $exception = new WebhookSignatureException('Custom message.');

        try {
            Extractor::fromJsonOrFail('"text"', $exception);
            $this->fail('Expected exception was not thrown.');
        } catch (WebhookSignatureException $e) {
            $this->assertSame($exception, $e);
        }
    }

    public function testStringReturnsString(): void
    {
        $this->assertSame('ORD-001', Extractor::string(['orderId' => 'ORD-001'], 'orderId'));
        $this->assertSame('', Extractor::string(['orderId' => ''], 'orderId'));
    }

    public function testStringConvertsNumbers(): void
    {
        $this->assertSame('1000', Extractor::string(['amount' => 1000], 'amount'));
        $this->assertSame('10.5', Extractor::string(['amount' => 10.5], 'amount'));
    }

    public function testStringReturnsNullForOtherTypes(): void
    {
        $data = ['bool' => true, 'array' => ['a'], 'null' => null];

        $this->assertNull(Extractor::string($data, 'bool'));
        $this->assertNull(Extractor::string($data, 'array'));
        $this->assertNull(Extractor::string($data, 'null'));
        $this->assertNull(Extractor::string($data, 'missing'));
    }

    public function testListReturnsArrayItems(): void
    {
        $data = ['orders' => [['orderId' => 'ORD-001'], ['orderId' => 'ORD-002']]];

        $this->assertSame([['orderId' => 'ORD-001'], ['orderId' => 'ORD-002']], Extractor::list($data, 'orders'));
    }

    public function testListSkipsItemsThatAreNotArrays(): void
    {
        $data = ['orders' => ['text', ['orderId' => 'ORD-001'], 123, null]];

        $this->assertSame([['orderId' => 'ORD-001']], Extractor::list($data, 'orders'));
    }

    public function testListDropsNumericKeysInItems(): void
    {
        $data = ['orders' => [['x', 'orderId' => 'ORD-001']]];

        $this->assertSame([['orderId' => 'ORD-001']], Extractor::list($data, 'orders'));
    }

    public function testListReturnsEmptyArrayWhenKeyIsMissingOrNotArray(): void
    {
        $this->assertSame([], Extractor::list([], 'orders'));
        $this->assertSame([], Extractor::list(['orders' => 'text'], 'orders'));
    }
}
