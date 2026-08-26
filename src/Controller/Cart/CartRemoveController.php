<?php

namespace App\Controller\Cart;

use App\Service\Cart\CartServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
class CartRemoveController {
    public function __invoke(string $id, Request $request, CartServiceInterface $cartService, RouterInterface $router) : Response
    {
        $cartService->remove($id);

        return new RedirectResponse(
            $router->generate('cart_index')
        );
    }
}
