<?php
namespace App\Application\Command;

use App\Domain\Interface\Provider\OutageProviderInterface;
use App\Domain\Interface\Telegram\NotificationSenderInterface;
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
        private readonly NotificationSenderInterface $notificationSender,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outages = $this->outageProvider->fetchOutages();
        $notifications = $this->outageProcessor->process($outages);

        $sent = $this->notificationSender->send($notifications);

        $output->writeln("<info>Successfully sent $sent notifications.</info>");
        return Command::SUCCESS;
    }
}
