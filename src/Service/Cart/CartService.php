<?php

namespace App\Service\Cart;

use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Entity\User\User;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

final class CartService implements CartServiceInterface {
    private const string CART_KEY = 'cart';

    public function __construct(
        private readonly RequestStack       $requestStack,
        private readonly Security           $security,
        private readonly CartRepository     $cartRepository,
        private readonly CartItemRepository $cartItemRepository,
        private readonly ProductRepository  $productRepository,
    ) {}

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function getSessionCart(): array
    {
        return $this->getSession()->get(self::CART_KEY, []);
    }

    public function getCart(?User $user = null): object|array
    {
        if ($user) {
            return $this->cartRepository->findOneBy(['user' => $user])
                ?? $this->createCartForUser($user);
        }

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


    public function add(string $productId, string $name, int $price, int $quantity = 1, ?array $paintingOption = null, ?User $user = null): void {

        $product = $this->productRepository->find($productId);
        if ($user) {
            $cart = $this->getCart($user);

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
            $cart[$productId]['price'] = $product->getPriceDiscount() * $cart[$productId]['quantity'];
        } else {
            $cart[$productId] = [
                'productId' => $productId,
                'name' => $name,
                'price' => $product->getPriceDiscount(),
                'quantity' => $quantity,
                'paintingOption' => $paintingOption,
            ];
        }

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(string $productId): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $cart = $this->getCart($user);
            if (!$cart) {
                return;
            }

            $product = $this->productRepository->find($productId);
            $item = $this->cartItemRepository->findOneBy([
                'cart' => $cart,
                'product' => $product,
            ]);

            if (!$item) {
                return;
            }

            $this->cartItemRepository->removeAndSave($item);
            $this->cartRepository->removeAndSave($cart);
            return;
        }

        $cart = $this->getSessionCart();

        if (!isset($cart[$productId])) {
            return;
        }

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

    public function mergeSessionCart(User $user): void
    {
        $sessionCart = $this->getSessionCart();

        if (!$user || !$sessionCart) {
            return;
        }

        $this->getCart($user);

        foreach ($sessionCart as $item) {
            $this->add($item['productId'], $item['name'], $item['price'], $item['quantity'], $item['paintingOption'], $user);
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

        $items = $this->getSessionCart();

        $cart = [];

        foreach ($items as $item) {
            $productId = $item['productId'];
            if (isset($item[$productId])) {
                $cart[$productId]['quantity'] += $item->getQuantity();
            } else{
                $cart[$productId] = [
                    'productId' => $productId,
                    'name' => $item['name'],
                    'price' => $item['price'] * $item['quantity'],
                    'quantity' => $item['quantity'],
                    'paintingOption' => null,
                ];
            }
        }

        return $cart;

    }

    public function decrease(string $productId): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $cart = $this->cartRepository->findOneBy(['user' => $user]);

            if (!$cart) return;

            $item = $this->cartItemRepository->findOneBy([
                'cart' => $cart,
                'product' => $productId,
            ]);

            if (!$item) return;

            $quantity = $item->getQuantity() - 1;

            if ($quantity <= 0) {
                $this->cartItemRepository->removeAndSave($item);
                return;
            }

            $item->setQuantity($quantity);
            $this->cartItemRepository->persistAndSave($item);
            return;
        }

        $cart = $this->getSessionCart();

        if (!isset($cart[$productId])) return;

        $cart[$productId]['quantity']--;

        if ($cart[$productId]['quantity'] <= 0) {
            unset($cart[$productId]);
        }

        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function increase(string $productId): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $cart = $this->cartRepository->findOneBy(['user' => $user]);

            if (!$cart) return;

            $item = $this->cartItemRepository->findOneBy([
                'cart' => $cart,
                'product' => $productId,
            ]);

            if (!$item) return;

            $item->setQuantity($item->getQuantity() + 1);
            $this->cartItemRepository->persistAndSave($item);
            return;
        }

        $cart = $this->getSessionCart();

        if (!isset($cart[$productId])) return;

        $cart[$productId]['quantity']++;

        $this->getSession()->set(self::CART_KEY, $cart);
    }
}
