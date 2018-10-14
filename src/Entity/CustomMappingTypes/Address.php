<?php
namespace App\Entity\CustomMappingTypes;

class Address
{
    private $street;
    private $city;
    private $postcode;

    public function __construct(string $city, string $postcode, string $street)
    {
        $this->city = $city;
        $this->postcode = $postcode;
        $this->street = $street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getStreet(): string
    {
        return $this->street;
    }
}