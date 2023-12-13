<?php

namespace Takemo101\Chubby\Console\Command;

use DateTimeInterface;

class ServeProcessRequestPool
{
    /**
     * constructor
     *
     * @param array<string,object{startedAt:DateTimeInterface,message:?string}> $requests
     */
    public function __construct(
        private array $requests = [],
    ) {
        //
    }

    /**
     * Push the request object.
     *
     * @param string $host
     * @param DateTimeInterface $startedAt
     * @param string|null $message
     * @return void
     */
    public function push(
        string $host,
        DateTimeInterface $startedAt,
        ?string $message = null,
    ): void {
        $this->requests[$host] = (object) [
            'startedAt' => $startedAt,
            'message' => $message,
        ];
    }

    /**
     * Pop the request object.
     *
     * @return object{startedAt:DateTimeInterface,message:?string}|null
     */
    public function pop(string $host): ?object
    {
        $request = $this->requests[$host] ?? null;

        unset($this->requests[$host]);

        return $request;
    }

    /**
     * Get the request array.
     *
     * @return array<string,object{startedAt:DateTimeInterface,message:?string}>
     */
    public function getRequests(): array
    {
        return $this->requests;
    }
}
