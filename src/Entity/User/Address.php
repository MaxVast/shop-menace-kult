<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Embeddable]
class Address
{
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $street = null;


    #[ORM\Column(length: 100, nullable: true)]
    public ?string $city = null;


    #[ORM\Column(length: 20, nullable: true)]
    public ?string $postalCode = null;


    #[ORM\Column(length: 100, nullable: true)]
    public ?string $country = null;
}
