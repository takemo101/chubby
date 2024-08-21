<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Psr\Clock\ClockInterface as PsrClockInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Clock\ApplicationClock;
use Takemo101\Chubby\Clock\Clock;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;

use function DI\get;

/**
 * Psr-20 Clock related.
 */
class ClockProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'clock';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions->add(
            [
                ApplicationClock::class => function (
                    ConfigRepository $config,
                ) {
                    /** @var string */
                    $timezone = $config->get('app.timezone', 'UTC');

                    return ApplicationClock::fromTimezoneString($timezone);
                },
                Clock::class => function (
                    ApplicationClock $clock,
                    Hook $hook,
                ) {
                    /** @var Clock */
                    $hooked = $hook->do(
                        tag: Clock::class,
                        parameter: $clock,
                    );

                    return $hooked;
                },
                PsrClockInterface::class => get(Clock::class),
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        //
    }
}
