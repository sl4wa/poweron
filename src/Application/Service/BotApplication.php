<?php

namespace App\Application\Service;

use App\Infrastructure\Telegram\Handlers\StopCommand;
use App\Infrastructure\Telegram\Handlers\SubscriptionConversation;
use App\Infrastructure\Telegram\Handlers\SubscriptionInfoCommand;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BotApplication
{
    private Nutgram $bot;

    public function __construct(
        Nutgram $bot,
        ContainerInterface $container
    ) {
        $this->bot = $bot;
        $this->bot->getContainer()->delegate($container);
    }

    public function run(): void
    {
        Conversation::refreshOnDeserialize();

        $this->bot->onCommand('start', SubscriptionConversation::class);
        $this->bot->onCommand('stop', StopCommand::class);
        $this->bot->onCommand('subscription', SubscriptionInfoCommand::class);

        $this->bot->run();
    }
}
