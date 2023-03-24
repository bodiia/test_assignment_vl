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

        $directories = $this->excludedRecurringDirectories(
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
    private function excludedRecurringDirectories(array $directories): array
    {
        sort($directories, SORT_LOCALE_STRING);

        $excluded = [];
        for ($i = 0; $i < count($directories); $i++) {
            $pathParts_i = explode(DIRECTORY_SEPARATOR, $directories[$i]);
            for ($j = $i + 1; $j < count($directories); $j++) {
                $pathParts_j = explode(DIRECTORY_SEPARATOR, $directories[$j]);
                if (empty(array_diff($pathParts_i, $pathParts_j))) {
                    $excluded[] = $directories[$j];
                }
            }
        }
        return array_diff($directories, $excluded);
    }
}