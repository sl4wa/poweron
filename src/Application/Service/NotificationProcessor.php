<?php

namespace App\Application\Service;

use App\Application\DTO\NotificationDTO;
use App\Application\Exception\NotificationSendException;
use App\Application\Interface\Service\NotificationSenderInterface;
use App\Domain\Interface\Repository\UserRepositoryInterface;

class NotificationProcessor
{
    public function __construct(
        private readonly NotificationSenderInterface $notificationSender,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @param NotificationDTO[] $notificationDTOs
     * @return int Number of successfully sent notifications
     */
    public function process(array $notificationDTOs): int
    {
        $sent = 0;

        foreach ($notificationDTOs as $dto) {
            try {
                $this->notificationSender->send($dto);

                $user = $this->userRepository->find($dto->userId);
                if ($user) {
                     $updatedUser = $user->withUpdatedOutageFromNotification($dto);
                     $this->userRepository->save($updatedUser);
                }

                $sent++;
            } catch (NotificationSendException $e) {
                if ($e->isBlocked()) {
                    $this->userRepository->remove($e->userId);
                }
            }
        }

        return $sent;
    }
}
