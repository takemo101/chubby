<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Closure;
use DI\Definition\Source\DefinitionSource;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Symfony\Component\Uid\Uuid;
use BadFunctionCallException;

/**
 * Provide processing using Closure can be set.
 */
final readonly class ClosureProvider implements Provider, ProviderNameable
{
    /** @var string */
    private string $name;

    /**
     * constructor
     *
     * @param null|Closure(Definitions):mixed $register
     * @param null|Closure(Application):void $boot
     * @param string|null $name Provider name.
     * @throws InvalidArgumentException
     */
    public function __construct(
        private ?Closure $register = null,
        private ?Closure $boot = null,
        ?string $name = null,
    ) {
        if (is_null($register) && is_null($boot)) {
            throw new InvalidArgumentException('register or boot must be set.');
        }

        $this->name = $name ?? Uuid::v6()->toRfc4122();
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
     * @throws BadFunctionCallException
     */
    public function register(Definitions $definitions): void
    {
        if (!$this->register) {
            return;
        }

        $result = call_user_func($this->register, $definitions);

        if (
            is_string($result)
            || is_array($result)
            || $result instanceof DefinitionSource
        ) {
            $definitions->add($result);
        }

        if (!is_null($result)) {
            throw new BadFunctionCallException('return value must be null or string or array or DefinitionSource.')
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
