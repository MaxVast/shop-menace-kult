<?php

namespace App\Entity\User;

use App\Entity\Traits\TimestampableEntityTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface {

    #[ORM\Id, ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(type:"string", length:180)]
    #[Assert\NotBlank]
    private string $username;

    #[ORM\Column(type:"string", length:180, unique:true)]
    #[Assert\NotBlank]
    private string $email;

    #[ORM\Column(type:'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type:'string')]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    use TimestampableEntityTrait;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->address = new Address();
    }

    public function getId(): ?Uuid { return $this->id;}

    public function getUsername(): string { return $this->username; }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string { return $this->email; }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string { return $this->password; }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAddress(): Address { return $this->address; }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string { return $this->getEmail(); }
}
