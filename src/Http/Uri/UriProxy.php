<?php

namespace Takemo101\Chubby\Http\Uri;

use Nyholm\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Stringable;

/**
 * Uri interface proxy and decorator.
 */
class UriProxy implements UriInterface, Stringable
{
    /**
     * constructor
     *
     * @param UriInterface $uri
     */
    final public function __construct(
        private UriInterface $uri,
    ) {
        //
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->uri->getScheme();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthority(): string
    {
        return $this->uri->getAuthority();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->uri->getUserInfo();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->uri->getHost();
    }

    /**
     * {@inheritdoc}
     *
     * @return integer|null
     */
    public function getPort(): ?int
    {
        return $this->uri->getPort();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->uri->getQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFragment(): string
    {
        return $this->uri->getFragment();
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withScheme(string $scheme): UriInterface
    {
        return new static($this->uri->withScheme($scheme));
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        return new static($this->uri->withUserInfo($user, $password));
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withHost(string $host): UriInterface
    {
        return new static($this->uri->withHost($host));
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withPort(?int $port): UriInterface
    {
        return new static($this->uri->withPort($port));
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withPath(string $path): UriInterface
    {
        return new static($this->uri->withPath($path));
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withQuery(string $query): UriInterface
    {
        return new static($this->uri->withQuery($query));
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function withFragment(string $fragment): UriInterface
    {
        return new static($this->uri->withFragment($fragment));
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->uri->__toString();
    }

    /**
     * Get the current URI.
     *
     * @return string
     */
    public function getCurrent(): string
    {
        return $this->getBase() . $this->getPath();
    }

    /**
     * Get the base URI.
     *
     * @return string
     */
    public function getBase(): string
    {
        $schema = $this->getScheme();
        $authority = $this->getAuthority();

        return ($schema
            ? $schema . ':'
            : ''
        ) . ($authority ? '//' . $authority : '');
    }

    /**
     * Get the copy of the URI.
     *
     * @return UriInterface
     */
    public function copy(): UriInterface
    {
        return new static($this->uri);
    }

    /**
     * Replace the URI.
     *
     * @param UriInterface $uri
     * @return void
     */
    public function replace(UriInterface $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Create a new instance from string.
     *
     * @param string $uri
     * @return static
     */
    public static function fromString(string $uri): static
    {
        return new static(new Uri($uri));
    }
}
