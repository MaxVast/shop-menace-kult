<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
class Category
{
    #[ORM\Id, ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank, Assert\Length(max: 64)]
    private string $name;


    #[ORM\OneToMany(targetEntity:Products::class, mappedBy:"category")]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts($products): void
    {
        $this->products = $products;
    }

    public function addProduct(Products $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }
    }

    public function removeProduct(Products $product): void
    {
        if ($this->products->contains($product)) {
            $product->setCategory(null);
            $this->products->removeElement($product);
        }
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
