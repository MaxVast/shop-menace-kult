<?php

namespace App\Repository;

use App\Entity\Cart\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function save(?CartItem $cartItem = null): void
    {
        $this->getEntityManager()->flush($cartItem);
    }

    public function persistAndSave(?CartItem $cartItem = null): void
    {
        $this->getEntityManager()->persist($cartItem);
        $this->save($cartItem);
    }
}
