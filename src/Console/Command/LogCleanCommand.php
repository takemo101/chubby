<?php

namespace Takemo101\Chubby\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;

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
     * @param ApplicationPath $path
     * @return integer
     */
    public function handle(
        OutputInterface $output,
        ConfigRepository $config,
        LocalFilesystem $filesystem,
        ApplicationPath $path,
    ) {
        /** @var string */
        $directory = $config->get('log.path', $path->getStoragePath('logs'));

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
