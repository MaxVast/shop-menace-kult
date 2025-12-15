<?php

namespace App\Entity\Media;

use App\Entity\Product\Products;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ProductsImage extends MediaObject
{
    #[Assert\Valid]
    #[ORM\ManyToOne(targetEntity: Products::class, inversedBy: 'images')]
    protected ?Products $products = null;

    #[Assert\NotNull, Assert\File(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])]
    public ?File $file;

    public function getProducts(): ?Products
    {
        return $this->products;
    }

    public function setProducts(Products $products): self
    {
        $products->addImage($this);
        $this->products = $products;

        return $this;
    }

    public function setRelation(object $object): self
    {
        if ($object instanceof Products) {
            $this->setProducts($object);

            return $this;
        }

        throw new \InvalidArgumentException(sprintf('Expected instance of %s, %s given.', self::getRelationClassname(), \gettype($object)));
    }

    public function getRelation(): Products
    {
        return $this->products;
    }

    public static function getRelationName(): string
    {
        return 'products';
    }

    public static function getRelationClassname(): string
    {
        return Products::class;
    }
}
