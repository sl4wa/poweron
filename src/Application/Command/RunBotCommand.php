<?php

namespace App\Application\Command;

use App\Application\Service\BotApplication;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:bot',
    description: 'Run the Telegram bot using Nutgram.'
)]
class RunBotCommand extends Command
{
    private BotApplication $botService;

    public function __construct(BotApplication $botService)
    {
        parent::__construct();
        $this->botService = $botService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting Telegram bot (Nutgram)...</info>');
        $this->botService->run();
        return Command::SUCCESS;
    }
}
