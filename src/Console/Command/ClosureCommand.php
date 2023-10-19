<?php

namespace Takemo101\Chubby\Console\Command;

use Closure;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Contract\ContainerInjectable;
use LogicException;

/**
 * Command that can be executed by a closure.
 */
final class ClosureCommand extends AbstractCommand implements ContainerInjectable
{
    /**
     * @var ApplicationContainer|null
     */
    private ?ApplicationContainer $container = null;

    /**
     * constructor
     *
     * @param Closure $closure
     * @param string|null $name
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        protected readonly Closure $closure,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    /**
     * Execute command process.
     *
     * @return integer
     */
    public function handle()
    {
        return $this->getContainer()->call($this->closure);
    }

    /**
     * Get application container.
     *
     * @return ApplicationContainer
     * @throws LogicException
     */
    protected function getContainer(): ApplicationContainer
    {
        return $this->container ?? throw new LogicException('container is not set!');
    }

    /**
     * Set the application container implementation.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function setContainer(ApplicationContainer $container): void
    {
        $this->container = $container;
    }

    /**
     * Create a new command from a Closure.
     *
     * @param Closure $closure
     * @return static
     */
    public static function from(
        Closure $closure,
        ?string $name = null,
    ): self {
        return new self($closure, $name);
    }
}
