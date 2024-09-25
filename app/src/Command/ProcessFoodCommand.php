<?php

declare(strict_types=1);

namespace App\Command;

use App\Factory\FoodFactory;
use App\Service\ResultTracker;
use Doctrine\ORM\EntityManagerInterface;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessFoodCommand extends Command
{
    protected static $defaultName = 'app:process-food';

    public function __construct(
        private readonly FoodFactory $foodFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Process food from JSON and add to collections')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $io->error('File not found.');

            return Command::FAILURE;
        }

        $jsonData = Items::fromFile($file, ['decoder' => new ExtJsonDecoder(true)]);

        if (!$io->confirm('Are you sure you want to process this file and truncate the current food data?', false)) {
            $io->warning('Command aborted.');

            return Command::FAILURE;
        }

        $this->entityManager->getConnection()->executeStatement('TRUNCATE TABLE food');

        $resultTracker = new ResultTracker();

        foreach ($jsonData as $item) {
            try {
                $this->foodFactory->create($item);

                $resultTracker->incrementSuccess();
            } catch (\Exception $e) {
                $resultTracker->incrementFailure();
                $io->error("Failed to add " . json_encode($item) . ": " . $e->getMessage());
            }
        }

        $io->success(sprintf(
            "Processing completed: %d succeeded, %d failed.",
            $resultTracker->getSuccessCount(),
            $resultTracker->getFailureCount()
        ));

        return Command::SUCCESS;
    }
}
