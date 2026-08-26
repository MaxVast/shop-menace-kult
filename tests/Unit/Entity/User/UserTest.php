<?php

namespace App\Tests\Entity\User;

use App\Entity\Cart\Cart;
use App\Entity\User\Address;
use App\Entity\User\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tests unitaires purs (pas de KernelTestCase, pas de DB) : on teste
 * uniquement le comportement de l'entité elle-même.
 */
class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(UserInterface::class, $this->user);
        $this->assertInstanceOf(PasswordAuthenticatedUserInterface::class, $this->user);
    }

    public function testConstructorInitializesDefaults(): void
    {
        $this->assertNull($this->user->getId());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->user->getUpdatedAt());
        $this->assertInstanceOf(Address::class, $this->user->getAddress());
        $this->assertNull($this->user->getCart());
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    public function testUsernameGetterAndSetter(): void
    {
        $this->user->setUsername('jdoe');

        $this->assertSame('jdoe', $this->user->getUsername());
    }

    public function testEmailGetterAndSetter(): void
    {
        $this->user->setEmail('jdoe@example.com');

        $this->assertSame('jdoe@example.com', $this->user->getEmail());
    }

    public function testGetUserIdentifierReturnsEmail(): void
    {
        $this->user->setEmail('jdoe@example.com');

        $this->assertSame('jdoe@example.com', $this->user->getUserIdentifier());
    }

    public function testPasswordGetterAndSetter(): void
    {
        $this->user->setPassword('hashed-password');

        $this->assertSame('hashed-password', $this->user->getPassword());
    }

    public function testEraseCredentialsDoesNotThrow(): void
    {
        // eraseCredentials() est vide mais doit être appelable sans erreur
        $this->user->eraseCredentials();
        $this->addToAssertionCount(1);
    }

    public function testDefaultRolesAlwaysContainRoleUser(): void
    {
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testSetRolesAlwaysAddsRoleUserEvenIfNotProvided(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);

        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testRolesAreUnique(): void
    {
        $this->user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_USER']);

        $roles = $this->user->getRoles();

        $this->assertCount(2, $roles);
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
    }

    public function testSetRolesReturnsSelfForFluentInterface(): void
    {
        $result = $this->user->setRoles(['ROLE_ADMIN']);

        $this->assertSame($this->user, $result);
    }

    public function testUpdatedAtGetterAndSetter(): void
    {
        $date = new \DateTime('2024-01-15 10:00:00');
        $this->user->setUpdatedAt($date);

        $this->assertSame($date, $this->user->getUpdatedAt());
    }

    public function testUpdatedAtCanBeSetToNull(): void
    {
        $this->user->setUpdatedAt(null);

        $this->assertNull($this->user->getUpdatedAt());
    }

    public function testCreatedAtIsNotNullAndNotSettable(): void
    {
        // Pas de setter public pour createdAt : on vérifie juste
        // qu'il est bien initialisé par le constructeur.
        $this->assertInstanceOf(\DateTimeInterface::class, $this->user->getCreatedAt());
    }

    public function testAddressIsInitializedAndNeverNull(): void
    {
        $address = $this->user->getAddress();

        $this->assertInstanceOf(Address::class, $address);
        // Même instance tant qu'aucun setter n'est appelé
        $this->assertSame($address, $this->user->getAddress());
    }

    public function testCartGetterAndSetter(): void
    {
        $cart = new Cart($this->user);

        $result = $this->user->setCart($cart);

        $this->assertSame($cart, $this->user->getCart());
        $this->assertSame($this->user, $result, 'setCart() doit retourner $this pour le chaînage');
    }

    public function testCartIsNullByDefault(): void
    {
        $this->assertNull($this->user->getCart());
    }
}
