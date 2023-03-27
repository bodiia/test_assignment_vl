<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Path;

final class SumCountFilesCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('sum:count')
            ->setDescription('Output sum of numbers from files')
            ->addArgument('directories', InputArgument::IS_ARRAY, 'List of directories for searching files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = $input->getArgument('directories');

        if (empty($directories)) {
            $output->writeln('List of directories is empty.');

            return Command::FAILURE;
        }

        $directories = $this->excludeRecurringDirectories($this->makeAbsoluteDirectoryPaths($directories));

        try {
            $result = $this->getTotalSumOfNumbersInFiles($this->getFilesFromDirectories($directories, ['count']));
        } catch (\InvalidArgumentException $exception) {
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }

        $output->writeln("Result: $result");

        return Command::SUCCESS;
    }

    private function makeAbsoluteDirectoryPaths(array $directories): array
    {
        $directoryPaths = [];
        foreach ($directories as $directory) {
            $directoryPaths[] = Path::makeAbsolute($directory, __DIR__ . '/../../');
        }
        return $directoryPaths;
    }

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

    /**
     * @return \SplFileInfo[]
     */
    private function getFilesFromDirectories(array $directories, array $filenames): array
    {
        $files = [];

        if (! $currentDirectory = array_shift($directories)) {
            return $files;
        }

        if (! file_exists($currentDirectory)) {
            throw new \InvalidArgumentException("No such directory: $currentDirectory");
        }

        $directoryIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($currentDirectory, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $fileInfo */
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isFile() && in_array(basename($fileInfo->getPathname()), $filenames)) {
                $files[] = $fileInfo;
            }
        }
        return [...$files, ...$this->getFilesFromDirectories($directories, $filenames)];
    }

    /**
     * @param \SplFileInfo[] $files
     */
    private function getTotalSumOfNumbersInFiles(array $files): int
    {
        $result = 0;
        foreach ($files as $file) {
            $result += $this->getSumOfNumbersInFileContents(file_get_contents($file->getPathname()));
        }
        return $result;
    }

    private function getSumOfNumbersInFileContents(string $content): int
    {
        $sumInLines = array_map(static function (string $line): int {
            return array_sum(explode(" ", $line));
        }, explode(PHP_EOL, $content));

        return array_sum($sumInLines);
    }
}
