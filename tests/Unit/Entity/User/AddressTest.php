<?php

namespace App\Tests\Unit\Entity\User;

use App\Entity\User\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    private Address $address;

    protected function setUp(): void
    {
        $this->address = new Address();
    }

    public function testAllFieldsAreNullByDefault(): void
    {
        $this->assertNull($this->address->street);
        $this->assertNull($this->address->city);
        $this->assertNull($this->address->postalCode);
        $this->assertNull($this->address->country);
    }

    public function testPropertiesCanBeAssignedDirectly(): void
    {
        $this->address->street = '12 rue des Lilas';
        $this->address->city = 'Belfort';
        $this->address->postalCode = '90000';
        $this->address->country = 'France';

        $this->assertSame('12 rue des Lilas', $this->address->street);
        $this->assertSame('Belfort', $this->address->city);
        $this->assertSame('90000', $this->address->postalCode);
        $this->assertSame('France', $this->address->country);
    }

    public function testFieldsCanBeSetBackToNull(): void
    {
        $this->address->city = 'Belfort';
        $this->address->city = null;

        $this->assertNull($this->address->city);
    }

    public function testEmptyStringIsAcceptedAsIsNoValidationOnEntity(): void
    {
        $this->address->street = '';

        $this->assertSame('', $this->address->street);
    }
}
