<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Support\ApplicationSummary;
use Symfony\Component\ErrorHandler\Debug;

/**
 * Error output related.
 */
class ErrorProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'error-output';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        //
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        /** @var ApplicationSummary */
        $summary = $app->get(ApplicationSummary::class);

        if ($summary->isDebugMode()) {
            Debug::enable();
        }
    }
}
