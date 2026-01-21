<?php

namespace App\Controller\Cart;

use App\Service\Cart\CartService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/cart/decrease/{id}', name: 'cart_decrease', methods: ['POST'])]
class CartDecreaseController {
    public function __invoke(string $id, CartService $cartService, RouterInterface $router): Response
    {
        $cartService->decrease($id);
        return new RedirectResponse(
            $router->generate('cart_index')
        );
    }
}
