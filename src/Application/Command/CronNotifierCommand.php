<?php

namespace App\Application\Command;

use App\Application\Interface\Provider\OutageProviderInterface;
use App\Application\Service\NotificationProcessor;
use App\Domain\Service\OutageProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:notifier',
    description: 'Fetch outages, prepare notifications, and send them via Telegram.',
)]
class CronNotifierCommand extends Command
{
    public function __construct(
        private readonly OutageProviderInterface $outageProvider,
        private readonly OutageProcessor $outageProcessor,
        private readonly NotificationProcessor $notificationProcessor,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outages = $this->outageProvider->fetchOutages();
        $notificationDTOs = $this->outageProcessor->process($outages);

        $sent = $this->notificationProcessor->process($notificationDTOs);

        $output->writeln("<info>Successfully sent $sent notifications.</info>");
        return Command::SUCCESS;
    }
}
