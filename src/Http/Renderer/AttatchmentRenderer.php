<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;
use Closure;

class AttatchmentRenderer extends AbstractStreamRenderer
{
    /** @var string */
    public const DefaultName = 'file';

    private ?Closure $finally;

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
        ?callable $finally = null,
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = []
    ) {
        $this->finally = is_null($finally)
            ? null
            : (
                $finally instanceof Closure
                ? $finally
                : Closure::fromCallable($finally)
            );

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
     * destructor
     */
    public function __destruct()
    {
        if ($finally = $this->finally) {
            call_user_func($finally, $this);
        }
    }

    /**
     * Create a renderer instance from a file path.
     *
     * @param string $path
     * @param string $name
     * @param string $mime
     * @param callable|null $finally
     * @param int $status
     * @param array<string,string> $headers
     * @return self
     */
    public static function fromPath(
        string|SplFileInfo $path,
        string $name = '',
        string $mime = '',
        ?callable $finally = null,
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = []
    ): self {
        $file = new SplFileInfo($path);

        return new self(
            $file,
            $name,
            $mime,
            $finally,
            $status,
            $headers
        );
    }
}
