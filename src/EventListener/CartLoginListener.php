<?php

namespace App\EventListener;

use App\Service\Cart\CartService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class CartLoginListener {
    public function __construct(
        private readonly CartService $cartService,
        private LoggerInterface $logger
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        throw new HttpException(500, 'LOGIN EVENT FIRED');

        //$this->logger->critical('🔥 LOGIN SUCCESS EVENT FIRED');

        //$this->cartService->mergeSessionCart();
    }
}
