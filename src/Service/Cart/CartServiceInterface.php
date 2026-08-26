<?php

namespace App\Service\Cart;

use App\Entity\User\User;

interface CartServiceInterface
{
    public function getSessionCart();

    public function getCart(?User $user = null);

    public function getItemsCount();

    public function add(string $productId, string $name, int $price, int $quantity = 1, ?array $paintingOption = null, ?User $user = null);

    public function remove(string $productId);

    public function clear();

    public function getTotal();

    public function mergeSessionCart(User $user);

    public function getItemsForCartView();

    public function decrease(string $productId);

    public function increase(string $productId);
}
