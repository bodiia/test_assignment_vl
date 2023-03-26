<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Path;

final class SumCountFilesCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    private readonly string $fixturePath;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('sum:count');
        $this->commandTester = new CommandTester($command);
        $this->fixturePath = __DIR__ . '/../../tests/Fixture/Command/SumCountFilesCommand';
    }

    public function testSuccessfullyExecute(): void
    {
        $this->commandTester->execute([
            'directories' => [
                Path::join($this->fixturePath, 'recurring'),
                Path::join($this->fixturePath, 'recurring', 'recurring_1'),
                Path::join($this->fixturePath, 'some_directory'),
                Path::join($this->fixturePath, 'some_directory_1'),
                Path::join($this->fixturePath, 'some_directory_2'),
            ]
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Result: 8', $this->commandTester->getDisplay());
    }

    public function testWithNonExistDirectories(): void
    {
        $this->commandTester->execute([
            'directories' => [
                Path::join($this->fixturePath, 'some_directory_not_exist'),
            ]
        ]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
