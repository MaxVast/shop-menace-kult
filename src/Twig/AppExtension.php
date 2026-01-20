<?php

namespace App\Twig;

use App\Service\Cart\CartService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly CartService $cartService) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_count', [$this, 'getCartCount']),
        ];
    }

    public function getCartCount(): int
    {
        return $this->cartService->getItemsCount();
    }
}

