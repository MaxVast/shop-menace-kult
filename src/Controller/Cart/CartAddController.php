<?php

namespace App\Controller\Cart;

use App\Entity\Product\Product;
use App\Service\Cart\CartService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
class CartAddController {
    public function __invoke(Product $product, Request $request, CartService $cart, RouterInterface $router): Response
    {
        $paintingOption = null;

        /*if ($request->request->get('painting_option')) {
            $paintingOption = [
                'id' => 1,
                'name' => 'Peinture premium',
                'price' => 2500,
            ];
        }*/

        $cart->add(
            $product->getId()->toRfc4122(),
            $product->getName(),
            $product->getPrice(),
            1,
            $paintingOption
        );

        return new RedirectResponse(
            $router->generate('cart_index')
        );

    }
}
