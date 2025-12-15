<?php

namespace App\Entity;

use App\Entity\Media\ProductsImage;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Products {

    #[ORM\Id, ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank, Assert\Length(max: 64)]
    private string $name;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 75, unique: true)]
    #[Assert\NotBlank]
    public string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Category::class, cascade:['persist'], fetch:'LAZY', inversedBy:'products',)]
    private ?Category $category;

    #[ORM\OneToMany(targetEntity: ProductsImage::class, mappedBy: 'products', cascade: ['remove', 'persist'], orphanRemoval: true)]
    #[Assert\Count(min: 1)]
    public iterable $images;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'int', nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $license_name = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $license_number = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $license_type = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    public bool $isDraft = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $active = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface  $updatedAt = null;

    public function __construct()
    {
        $createdAt = new \DateTime();
        $this->images = new ArrayCollection();
    }

    /**
     * @return Uuid|null
     */
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
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

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $stock
     */
    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return string|null
     */
    public function getLicenseName(): ?string
    {
        return $this->license_name;
    }

    /**
     * @param string|null $license_name
     */
    public function setLicenseName(?string $license_name): void
    {
        $this->license_name = $license_name;
    }

    /**
     * @return string|null
     */
    public function getLicenseNumber(): ?string
    {
        return $this->license_number;
    }

    /**
     * @param string|null $license_number
     */
    public function setLicenseNumber(?string $license_number): void
    {
        $this->license_number = $license_number;
    }

    /**
     * @return string|null
     */
    public function getLicenseType(): ?string
    {
        return $this->license_type;
    }

    /**
     * @param string|null $license_type
     */
    public function setLicenseType(?string $license_type): void
    {
        $this->license_type = $license_type;
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    /**
     * @param bool $isDraft
     */
    public function setIsDraft(bool $isDraft): void
    {
        $this->isDraft = $isDraft;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedAt
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}
