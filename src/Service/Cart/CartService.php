<?php

namespace App\Service\Cart;

use Symfony\Component\HttpFoundation\RequestStack;

final class CartService {
    private const CART_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack
    ) {}

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function getCart(): array
    {
        return $this->getSession()->get(self::CART_KEY, []);
    }

    public function getItemsCount(): int
    {
        return array_sum(
            array_column($this->getCart(), 'quantity')
        );
    }

    public function add(
        string $productId,
        string $name,
        int $price,
        int $quantity = 1,
        ?array $paintingOption = null
    ): void {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'productId' => $productId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'paintingOption' => $paintingOption,
            ];
        }

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(string $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::CART_KEY);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $item) {
            $lineTotal = $item['price'] * $item['quantity'];

            if (!empty($item['paintingOption'])) {
                $lineTotal += $item['paintingOption']['price'];
            }

            $total += $lineTotal;
        }

        return $total;
    }
}
