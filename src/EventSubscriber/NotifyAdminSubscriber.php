<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\PostCommentedEvent;
use App\Lib\Notification\TelegramNotifier;
use App\Service\UriResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotifyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly bool $areNotificationsEnabled,
        private readonly int $adminTelegramChatId,
        private readonly UriResolver $uriResolver,
        private readonly TelegramNotifier $notifier
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [PostCommentedEvent::class => 'onPostCommented'];
    }

    public function onPostCommented(PostCommentedEvent $event): void
    {
        if (!$this->areNotificationsEnabled) {
            return;
        }

        if ($event->repliedTo() !== '') {
            $threadUri    = $this->uriResolver->resolveThreadUri($event->postSlug(), $event->repliedTo());
            $notification = sprintf('New reply to %s :' . PHP_EOL, $threadUri);
        } else {
            $postUri      = $this->uriResolver->resolvePostUri($event->postSlug());
            $notification = sprintf('New comment to %s :' . PHP_EOL, $postUri);
        }

        $notification .= $event->comment();

        $this->notifier->sendNotification($this->adminTelegramChatId, $notification);
    }
}
