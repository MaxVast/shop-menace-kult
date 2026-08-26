<?php

namespace App\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\MappedSuperclass]
abstract class MediaObject implements MediaObjectInterface
{
    #[ORM\Id, ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $originalName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $mimeType = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $tag = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $size = null;

    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $dimensions = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getModelName(): string
    {
        return $this->tag.'_'.$this->getId();
    }
}
