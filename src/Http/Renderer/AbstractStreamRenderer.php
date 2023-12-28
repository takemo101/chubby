<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;
use RuntimeException;
use DateTime;
use DateTimeZone;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Contract\ContainerInjectable;

/**
 * reference: https://github.com/symfony/http-foundation
 */
abstract class AbstractStreamRenderer implements ResponseRenderer, ContainerInjectable
{
    /** @var string */
    public const DefaultContentType = 'application/octet-stream';

    /**
     * @var ApplicationContainer|null
     */
    private ?ApplicationContainer $container = null;

    /**
     * @var boolean
     */
    private bool $autoEtag = false;

    /**
     * @var boolean
     */
    private bool $autoLastModified = true;

    /**
     * constructor
     *
     * @param string|resource|SplFileInfo|StreamInterface $data
     * @param string $mime
     * @param int $status
     * @param array<string,string> $headers
     */
    public function __construct(
        private mixed $data,
        private string $mime = '',
        private int $status = StatusCodeInterface::STATUS_OK,
        private array $headers = []
    ) {
        //
    }

    /**
     * Set data to be rendered.
     *
     * @param string|resource|SplFileInfo|StreamInterface $data
     * @return static
     */
    public function setData(mixed $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data to be rendered.
     *
     * @return string|resource|SplFileInfo|StreamInterface
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Set mime type to be rendered.
     *
     * @param string $mime
     * @return static
     */
    public function setMimeType(string $mime): static
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime type to be rendered.
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mime;
    }

    /**
     * Set status code to be rendered.
     *
     * @param int $status
     * @return static
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set headers to be rendered.
     *
     * @param array<string,string> $headers
     * @return static
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set auto etag.
     *
     * @param boolean $autoEtag
     * @return static
     */
    public function enableAutoEtag(bool $autoEtag = true): static
    {
        $this->autoEtag = $autoEtag;

        return $this;
    }

    /**
     * Set etag.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function setEtag(ResponseInterface $response): ResponseInterface
    {
        $data = $this->getData();

        if ($data instanceof SplFileInfo) {
            $tag = base64_encode(
                hash_file('sha256', $data->getPathname(), true) ?: throw new RuntimeException('Failed to generate hash.')
            );

            $response = $response->withHeader('ETag', $tag);
        }

        return $response;
    }

    /**
     * Set auto last modified.
     *
     * @param boolean $autoLastModified
     * @return static
     */
    public function enableAutoLastModified(bool $autoLastModified = true): static
    {
        $this->autoLastModified = $autoLastModified;

        return $this;
    }

    /**
     * Set last modified.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function setLastModified(ResponseInterface $response): ResponseInterface
    {
        $data = $this->getData();

        if ($data instanceof SplFileInfo) {

            if (false === $date = DateTime::createFromFormat('U', (string) $data->getMTime())) {
                throw new RuntimeException('Failed to create DateTime object.');
            }

            $date = $date
                ->setTimezone(new DateTimeZone('UTC'));

            $response = $response->withHeader('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');
        }

        return $response;
    }

    /**
     * Set the ApplicationContainer implementation.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function setContainer(ApplicationContainer $container): void
    {
        $this->container = $container;
    }

    /**
     * Get the ApplicationContainer implementation.
     *
     * @return ApplicationContainer
     */
    protected function getContainer(): ApplicationContainer
    {
        return $this->container ?? throw new RuntimeException('ApplicationContainer is not set.');
    }

    /**
     * Get the StreamFactoryInterface implementation.
     *
     * @return StreamFactoryInterface
     */
    private function getStreamFactory(): StreamFactoryInterface
    {
        /** @var StreamFactoryInterface */
        $streamFactory = $this->getContainer()->get(StreamFactoryInterface::class);

        return $streamFactory;
    }

    /**
     * Perform response writing process.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $stream = $this->getStream();

        $response = $this->configureResponse(
            $response
                ->withStatus($this->status)
                ->withHeader('Content-Type', $this->getContentType())
                ->withHeader('Content-Length', (string)$stream->getSize())
        );

        if ($this->autoEtag) {
            $response = $this->setEtag($response);
        }

        if ($this->autoLastModified) {
            $response = $this->setLastModified($response);
        }

        return $response->withBody($stream);
    }

    /**
     * Get content type to be rendered.
     *
     * @return string
     */
    protected function getContentType(): string
    {
        return $this->getMimeType() ?: static::DefaultContentType;
    }

    /**
     * Configure the response.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function configureResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    /**
     * Get stream from data.
     *
     * @return StreamInterface
     */
    protected function getStream(): StreamInterface
    {
        $data = $this->getData();

        if ($data instanceof StreamInterface) {
            return $data;
        }

        if ($data instanceof SplFileInfo) {
            return $this->getStreamFactory()->createStreamFromFile($data->getPathname());
        }

        if (is_string($data)) {
            return $this->getStreamFactory()->createStream($data);
        }

        if (is_resource($data)) {
            return $this->getStreamFactory()->createStreamFromResource($data);
        }

        throw new RuntimeException('Invalid data type.');
    }
}
