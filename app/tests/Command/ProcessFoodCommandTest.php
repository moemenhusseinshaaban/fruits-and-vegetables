<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ProcessFoodCommand;
use App\Factory\FoodFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessFoodCommandTest extends TestCase
{
    private $foodFactory;
    private $entityManager;
    private $connection;
    private $commandTester;

    protected function setUp(): void
    {
        /** @var FoodFactory $foodFactory */
        $this->foodFactory = $this->createMock(FoodFactory::class);
        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        /** @var Connection $connection */
        $this->connection = $this->createMock(Connection::class);

        $command = new ProcessFoodCommand($this->foodFactory, $this->entityManager);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithValidFile(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'json_');
        file_put_contents($filePath, json_encode([['name' => 'Apple'], ['name' => 'Banana']]));

        $this->connection
            ->expects($this->once())
            ->method('executeStatement')
            ->with('TRUNCATE TABLE food');

        $this->entityManager
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connection);
            
        $this->foodFactory
            ->expects($this->exactly(2))
            ->method('create');

        $this->commandTester->setInputs(['yes']);
        $this->commandTester->execute(['file' => $filePath]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Processing completed: 2 succeeded, 0 failed.', $output);

        unlink($filePath);
    }

    public function testHandleExceptionsDuringProcessing(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'json_');
        file_put_contents($filePath, json_encode([['name' => 'Apple']]));

        $this->connection
            ->expects($this->once())
            ->method('executeStatement')
            ->with('TRUNCATE TABLE food');

        $this->entityManager
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connection);

        $this->foodFactory
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new \Exception('Creation error'));

        $this->commandTester->setInputs(['yes']);
        $this->commandTester->execute(['file' => $filePath]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Processing completed: 0 succeeded, 1 failed.', $output);
        $this->assertStringContainsString('Failed to add', $output);

        unlink($filePath);
    }

    public function testFileNotFound(): void
    {
        $this->commandTester->execute(['file' => 'invalid-file.json']);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('File not found.', $output);

        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testUserAborts(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'json_');
        file_put_contents($filePath, json_encode([['name' => 'Apple'], ['name' => 'Banana']]));

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute(['file' => $filePath]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Command aborted.', $output);

        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());

        unlink($filePath);
    }
}
