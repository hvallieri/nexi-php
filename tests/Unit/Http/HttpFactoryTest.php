<?php declare(strict_types=1);

namespace Hval\Nexi\Tests\Unit\Http;

use Hval\Nexi\Http\HttpFactory;
use Hval\Nexi\Http\HttpFactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 * @covers HttpFactory
 */
class HttpFactoryTest extends TestCase
{
    /** @var HttpFactory */
    private $factory;

    protected function setUp(): void
    {
        $psr17 = new Psr17Factory();
        $this->factory = new HttpFactory($psr17, $psr17);
    }

    public function testImplementsHttpFactoryInterface(): void
    {
        $this->assertInstanceOf(HttpFactoryInterface::class, $this->factory);
    }

    public function testCreateRequestReturnsRequestInterface(): void
    {
        $request = $this->factory->createRequest('GET', 'https://example.com');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://example.com', (string) $request->getUri());
    }

    public function testCreateStreamReturnsStreamInterface(): void
    {
        $stream = $this->factory->createStream('hello');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame('hello', (string) $stream);
    }

    public function testRequestAndStreamFactoriesCanBeDifferentObjects(): void
    {
        $psr17 = new Psr17Factory();
        $factory = new HttpFactory($psr17, $psr17);

        $this->assertInstanceOf(RequestInterface::class, $factory->createRequest('POST', 'https://example.com'));
        $this->assertInstanceOf(StreamInterface::class, $factory->createStream('body'));
    }

    public function testCreateStreamFromFileReturnsStreamInterface(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'hval_test_');
        file_put_contents($file, 'file content');

        $stream = $this->factory->createStreamFromFile($file, 'r');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame('file content', (string) $stream);

        unlink($file);
    }

    public function testCreateStreamFromResourceReturnsStreamInterface(): void
    {
        $resource = fopen('php://memory', 'r+');
        fwrite($resource, 'resource content');
        rewind($resource);

        $stream = $this->factory->createStreamFromResource($resource);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame('resource content', (string) $stream);
    }
}
