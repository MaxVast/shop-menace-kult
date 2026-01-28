<?php

namespace App\Repository;

use App\Entity\Cart\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function save(?Cart $cart = null): void
    {
        $this->getEntityManager()->flush($cart);
    }

    public function persistAndSave(?Cart $cart = null): void
    {
        $this->getEntityManager()->persist($cart);
        $this->save($cart);
    }
}
