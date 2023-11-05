<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

final class AttatchmentRenderer extends AbstractStreamRenderer
{
    /** @var string */
    public const DefaultName = 'file';

    /**
     * constructor
     *
     * @param string|resource|SplFileInfo|StreamInterface $data
     * @param string $name
     * @param string $mime
     * @param int $status
     * @param array<string,string> $headers
     */
    public function __construct(
        mixed $data,
        private string $name = '',
        string $mime = '',
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = []
    ) {
        parent::__construct($data, $mime, $status, $headers);
    }

    /**
     * Set file name to be rendered.
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get file name to be rendered.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get file name to be rendered.
     *
     * @return string
     */
    private function getAttachmentName(): string
    {
        if ($name = $this->getName()) {
            return $name;
        }

        $data = $this->getData();

        if ($data instanceof SplFileInfo) {
            return $data->getFilename();
        }

        return self::DefaultName;
    }

    /**
     * Configure the response.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function configureResponse(ResponseInterface $response): ResponseInterface
    {
        $name = $this->getAttachmentName();

        return $response->withHeader(
            'Content-Disposition',
            'attachment; filename="' . addcslashes($name, '"') . '"'
        );
    }

    /**
     * Create a renderer instance from a file path.
     *
     * @param string $path
     * @param string $name
     * @param string $mime
     * @param int $status
     * @param array<string,string> $headers
     * @return static
     */
    public static function fromPath(
        string|SplFileInfo $path,
        string $name = '',
        string $mime = '',
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = []
    ): static {
        $file = new SplFileInfo($path);

        return new static(
            $file,
            $name,
            $mime,
            $status,
            $headers
        );
    }
}
