<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

final class SumCountFilesCommand extends Command
{
    public function __construct(private readonly Filesystem $filesystem)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('sum:count')
            ->addArgument('directories', InputArgument::IS_ARRAY, 'List of directories for searching files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = $input->getArgument('directories');

        if (empty($directories)) {
            $output->writeln('List of directories is empty.');

            return Command::FAILURE;
        }

        $directories = $this->excludeRecurringDirectories(
            $this->makeAbsoluteDirectoryPaths($directories)
        );

        if (! $this->filesystem->exists($directories)) {
            $output->writeln('Some directories not exists!');

            return Command::FAILURE;
        }

        $result = $this->getTotalSumOfNumbersInFiles(
            Finder::create()->in($directories)->files()->name('count')
        );
        $output->writeln("Result: $result");

        return Command::SUCCESS;
    }

    private function getTotalSumOfNumbersInFiles(\Traversable $files): int
    {
        $result = 0;
        foreach ($files as $file) {
            $result += (int) $file->getContents();
        }
        return $result;
    }

    private function makeAbsoluteDirectoryPaths(array $directories): array
    {
        $directoryPaths = [];
        foreach ($directories as $directory) {
            $directoryPaths[] = Path::makeAbsolute($directory, __DIR__ . '/../../');
        }
        return $directoryPaths;
    }

    /**
     *  Example: ['./test', './test/test_1'] => ['./test']
     */
    private function excludeRecurringDirectories(array $directories): array
    {
        $explodedDirectoryPaths = array_map(static function (string $directory): array {
            return explode(DIRECTORY_SEPARATOR, $directory);
        }, $directories);

        usort($explodedDirectoryPaths, static function (array $a, array $b): int {
            return count($a) <=> count($b);
        });

        $excluded = [];
        for ($i = 0; $i < count($explodedDirectoryPaths); $i++) {
            for ($j = $i + 1; $j < count($explodedDirectoryPaths); $j++) {
                if (empty(array_diff($explodedDirectoryPaths[$i], $explodedDirectoryPaths[$j]))) {
                    $excluded[] = implode(DIRECTORY_SEPARATOR, $explodedDirectoryPaths[$j]);
                }
            }
        }
        return array_diff($directories, $excluded);
    }
}