<?php

namespace Takemo101\Chubby\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Filesystem\LocalFilesystem;

/**
 * Command to delete log files
 */
#[AsCommand(
    name: 'log:clean',
    description: 'Delete log files',
)]
class LogCleanCommand extends Command
{
    /**
     * Execute command process.
     *
     * @param OutputInterface $output
     * @param ConfigRepository $config
     * @param LocalFilesystem $filesystem
     * @return integer
     */
    public function handle(
        OutputInterface $output,
        ConfigRepository $config,
        LocalFilesystem $filesystem,
    ) {
        /** @var string */
        $directory = $config->get('log.path', storage_path('logs'));

        if (!$filesystem->exists($directory)) {
            $output->writeln('<info>Log directory not found.</info>');
            return self::SUCCESS;
        }

        $files = $filesystem->glob($directory . '/*') ?? [];

        foreach ($files as $file) {
            $filesystem->delete($file);
        }

        $output->writeln('<info>Log files deleted.</info>');
        return self::SUCCESS;
    }
}
