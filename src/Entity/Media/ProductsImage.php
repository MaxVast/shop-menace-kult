<?php

namespace App\Entity\Media;

use App\Entity\Product\Products;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity, ORM\Table(name: 'products_image')]
#[Vich\Uploadable]
class ProductsImage extends MediaObject
{
    #[Assert\Valid]
    #[ORM\ManyToOne(targetEntity: Products::class, inversedBy: 'images')]
    protected ?Products $products = null;

    #[Assert\File(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])]
    #[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'name')]
    public ?File $file = null;

    /**
     * @param File|null $file
     */
    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if ($file) {
            $this->updatedAt = new \DateTimeImmutable();
            $this->originalName = $file->getFilename();
            $this->mimeType = $file->getMimeType();
            $this->size = $file->getSize();
            $this->dimensions = null;
        }
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getProducts(): ?Products
    {
        return $this->products;
    }

    public function setProducts(Products $products): self
    {
        $products->addImage($this);
        $this->products = $products;

        $products->setUpdatedAt(new \DateTimeImmutable());

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
