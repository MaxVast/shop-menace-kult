<?php

namespace App\Entity\Media;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

abstract class MediaObject implements MediaObjectInterface
{
    #[ORM\Id, ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    public ?string $name;

    #[ORM\Column(type: 'string')]
    public ?string $originalName;

    #[ORM\Column(type: 'string')]
    public ?string $mimeType;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $tag = null;

    #[ORM\Column(type: 'integer')]
    public ?int $size;

    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $dimensions;

    #[Assert\NotNull]
    public ?File $file;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v6();
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
