<?php

namespace App\EventListener;

use App\Service\Cart\CartServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

readonly class CartLoginListener
{
    public function __construct(
        private CartServiceInterface $cartService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $this->logger->info('AUTH SUCCESS EVENT TRIGGERED');

        $user = $event->getAuthenticationToken()?->getUser();

        if (!is_object($user)) {
            return;
        }

        $this->cartService->mergeSessionCart($user);
    }
}
