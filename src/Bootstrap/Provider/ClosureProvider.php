<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Closure;
use DI\Definition\Source\DefinitionSource;
use Takemo101\Chubby\Bootstrap\Definitions;
use Symfony\Component\Uid\Uuid;
use BadFunctionCallException;
use InvalidArgumentException;
use Takemo101\Chubby\ApplicationContainer;

/**
 * Provide processing using Closure can be set.
 */
class ClosureProvider implements Provider, ProviderNameable
{
    /** @var string */
    private string $name;

    /**
     * constructor
     *
     * @param null|Closure(Definitions):mixed $register
     * @param null|Closure(ApplicationContainer):void $boot
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

        $this->name = $name ?? $this->createUniqueName();
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
            return;
        }
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        if (!$this->boot) {
            return;
        }

        // The callable value set in the property boot is executed by the InvokerInterface
        $container->call($this->boot, [
            'container' => $container,
        ]);
    }

    /**
     * Create a unique provider name.
     *
     * @return string
     */
    private function createUniqueName(): string
    {
        return Uuid::v6()->toRfc4122();
    }
}
