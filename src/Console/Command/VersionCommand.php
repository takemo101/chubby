<?php

namespace Takemo101\Chubby\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Application;

/**
 * Display version.
 */
#[AsCommand(
    name: 'version',
    description: 'Display version.',
)]
class VersionCommand extends Command
{
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
