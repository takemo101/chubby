<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Closure;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use InvalidArgumentException;

/**
 * Provide processing using Closure can be set.
 */
final readonly class ClosureProvider implements Provider, ProviderNameable
{
    /**
     * constructor
     *
     * @param string $name Provider name.
     * @param null|Closure(Definitions):mixed $register
     * @param null|Closure(Application):void $boot
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $name,
        private ?Closure $register = null,
        private ?Closure $boot = null,
    ) {
        if (is_null($register) && is_null($boot)) {
            throw new InvalidArgumentException('register or boot must be set.');
        }
    }

    /**
     * Get provider name.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->name;
    }

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        if (!$this->register) {
            return;
        }

        $result = call_user_func($this->register, $definitions);

        if (is_array($result)) {
            $definitions->add($result);
        }
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        if (!$this->boot) {
            return;
        }

        call_user_func($this->boot, $app);
    }
}
