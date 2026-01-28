<?php

namespace App\EventListener;

use App\Service\Cart\CartService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class CartLoginListener {
    public function __construct(
        private readonly CartService $cartService,
        private LoggerInterface $logger
    ) {}

    public function __invoke(AuthenticationSuccessEvent  $event): void
    {
        $this->logger->info('AUTH SUCCESS EVENT TRIGGERED');

        $user = $event->getAuthenticationToken()?->getUser();

        if (!is_object($user)) {
            return;
        }

        $this->cartService->mergeSessionCart($user);
    }
}
