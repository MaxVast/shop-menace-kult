<?php

namespace App\Entity\Product;

use App\Entity\Media\ProductsImage;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    public const array STATUSES = ['draft', 'published', 'archived'];

    #[ORM\Id, ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 65)]
    #[Assert\NotBlank, Assert\Length(max: 65)]
    private string $name;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 75, unique: true)]
    #[Assert\NotBlank]
    public string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'products_categories')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $categories;

    #[ORM\OneToMany(targetEntity: ProductsImage::class, mappedBy: 'products', cascade: ['remove', 'persist'], orphanRemoval: true)]
    #[Assert\Count(min: 1, max: 10)]
    public Collection $images;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $discount = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $lot = true;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $printOnDemand = true;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $paintingOnDemand = true;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $productionDelayDays = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $license_name = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $license_number = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $license_type = null;

    #[ORM\Column]
    #[Assert\NotBlank, Assert\Choice(Product::STATUSES)]
    private string $status = 'draft';

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->images = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductsImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProducts($this);
        }

        return $this;
    }

    public function removeImage(ProductsImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): void
    {
        $this->discount = null !== $discount ? (int) $discount : null;
    }

    public function getPriceDiscount(): float
    {
        if (null === $this->discount) {
            return $this->price / 100;
        }

        return ($this->price * (1 - $this->discount / 100)) / 100;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    public function isLot(): bool
    {
        return $this->lot;
    }

    public function setLot(bool $lot): void
    {
        $this->lot = $lot;
    }

    public function isPrintOnDemand(): bool
    {
        return $this->printOnDemand;
    }

    public function setPrintOnDemand(bool $printOnDemand): void
    {
        $this->printOnDemand = $printOnDemand;
    }

    public function isPaintingOnDemand(): bool
    {
        return $this->paintingOnDemand;
    }

    public function setPaintingOnDemand(bool $paintingOnDemand): self
    {
        $this->paintingOnDemand = $paintingOnDemand;

        return $this;
    }

    public function getProductionDelayDays(): ?int
    {
        return $this->productionDelayDays;
    }

    public function setProductionDelayDays(?int $productionDelayDays): void
    {
        $this->productionDelayDays = $productionDelayDays;
    }

    public function getLicenseName(): ?string
    {
        return $this->license_name;
    }

    public function setLicenseName(?string $license_name): void
    {
        $this->license_name = $license_name;
    }

    public function getLicenseNumber(): ?string
    {
        return $this->license_number;
    }

    public function setLicenseNumber(?string $license_number): void
    {
        $this->license_number = $license_number;
    }

    public function getLicenseType(): ?string
    {
        return $this->license_type;
    }

    public function setLicenseType(?string $license_type): void
    {
        $this->license_type = $license_type;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
