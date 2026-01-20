<?php

namespace App\Service\Cart;

use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

final class CartService {
    private const CART_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private CartRepository $cartRepository,
        private CartItemRepository $cartItemRepository,
        private ProductRepository $productRepository,
    ) {}

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function getSessionCart(): array
    {
        return $this->getSession()->get(self::CART_KEY, []);
    }

    public function getCart(): Cart|array
    {
        $user = $this->security->getUser();

        if ($user) {
            return $this->cartRepository->findOneBy(['user' => $user])
                ?? $this->createCartForUser($user);
        }

        return $this->getSession()->get(self::CART_KEY, []);
    }


    public function getItemsCount(): int
    {
        if ($this->security->getUser()) {
            $cart = $this->cartRepository->findOneBy(['user' => $this->security->getUser()]);
            $items = $this->cartItemRepository->findBy(['cart' => $cart]);

            return array_sum(
                array_map(fn(CartItem $item) => $item->getQuantity(), $items)
            );
        }

        return array_sum(
            array_column($this->getSessionCart(), 'quantity')
        );
    }


    public function add(string $productId, string $name, int $price, int $quantity = 1, ?array $paintingOption = null): void {

        if ($this->security->getUser()) {
            $cart = $this->getCart();
            $product = $this->productRepository->find($productId);

            $item = $this->cartItemRepository->findOneBy([
                'cart' => $cart,
                'product' => $product,
            ]);

            if ($item) {
                $item->setQuantity($item->getQuantity() + 1);
            } else {

                $item = new CartItem();

                $item->setCart($cart);
                $item->setProduct($product);
                $item->setQuantity(1);
            }

            $this->cartItemRepository->persistAndSave($item);
            return;
        }

        $cart = $this->getSessionCart();
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
        if ($this->security->getUser()) {
            $cart = $this->getCart();
            $items = $this->cartItemRepository->findBy(['cart' => $cart]);

            return array_sum(
                array_map(fn(CartItem $item) =>
                    $item->getProduct()->getPriceDiscount() * $item->getQuantity(),
                    $items
                )
            );
        }

        return array_sum(
            array_map(fn($item) => $item['price'] * $item['quantity'], $this->getSessionCart())
        );
    }

    public function mergeSessionCart(): void
    {
        if (!$this->security->getUser()) return;

        foreach ($this->getSessionCart() as $item) {
            $this->add($item['productId'], $item['quantity'], $item['paintingOption']);
        }

        $this->clear();
    }

    private function createCartForUser($user): Cart
    {
        $cart = new Cart($user);
        $this->cartRepository->persistAndSave($cart);

        return $cart;
    }

    public function getItemsForCartView() : array
    {
        if ($this->security->getUser()) {
            $dataCart = $this->getCart();
            $items = $this->cartItemRepository->findBy(['cart' => $dataCart]);

            $cart = [];

            foreach ($items as $item) {
                $productId = $item->getProduct()->getId()->toRfc4122();
                if (isset($cart[$productId])) {
                    $cart[$productId]['quantity'] += $item->getQuantity();
                } else{
                    $cart[$productId] = [
                        'productId' => $productId,
                        'name' => $item->getProduct()->getName(),
                        'price' => $item->getProduct()->getPriceDiscount() * $item->getQuantity(),
                        'quantity' => $item->getQuantity(),
                        'paintingOption' => null,
                    ];
                }
            }

            return $cart;
        }

        return $this->getSessionCart();

    }
}
