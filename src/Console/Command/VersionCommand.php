<?php

namespace Takemo101\Chubby\Console\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Application;

/**
 * Display version.
 */
final class VersionCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('version')
            ->setDescription('Display version');
    }

    /**
     * Execute command process.
     *
     * @param OutputInterface $output
     * @return integer
     */
    public function handle(OutputInterface $output)
    {
        $output->writeln('<info>------------------</info>');
        $output->writeln(Application::Name . ' <comment>' . Application::Version . '</comment>');
        $output->writeln('Enjoy!');
        $output->writeln('<info>------------------</info>');

        return self::SUCCESS;
    }
}
