<?php

namespace App\Controller\Cart;

use App\Service\Cart\CartServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/cart', name: 'cart_index', methods: ['GET', 'POST'])]
class CartIndexController {
    public function __invoke(Environment $twig, CartServiceInterface $cartService): Response
    {
        return new Response($twig->render('cart/cart.html.twig', [
            'cart' => $cartService->getItemsForCartView(),
            'total' => $cartService->getTotal(),
        ]), Response::HTTP_OK);
    }
}
