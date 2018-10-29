<?php

namespace App\Tests\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\CustomMappingTypes\CountryId;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\Country;
use App\Entity\PersistenceModel\House;
use App\Entity\PersistenceModel\User;

class UserBuilder
{
	/** @var string */
	private $name;

	/** @var string */
	private $surname;

	/** @var Car */
	private $car;

	/** @var Address */
	private $address;

	/** @var Country */
	private $country;

	/** @var House */
	private $house;


	public static function any()
	{
		$name = 'Francisco';
		$surname = 'surname1 surname2';
		$car = new Car('Renault', 'black');
		$address = new Address('Madrid', '23NRR', 'McShit Square');
		$country = new Country('Africa', 2323);
		$house = null;

		return new self($name, $surname, $car, $address, $country,  $house);

	}

	public function withName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function withSurname($surname): self
	{
		$this->surname = $surname;
		return $this;
	}

	public function withCar(Car $car)
	{
		$this->car = $car;
		return $this;
	}

	public function withAddress(Address $address)
	{
		$this->address = $address;
		return $this;
	}

	public function withCountry(Country $country)
	{
		$this->country = $country;
		return $this;
	}

	public function withHouse(?House $house)
	{
		$this->house = $house;
		return $this;
	}


	public function build()
	{
		return new User(
			$this->name,
			$this->surname,
			$this->car,
			$this->address,
			$this->country,
			$this->house
		);
	}

	private function __construct(string $name, string $surname, Car $car, Address $address, Country $country, ?House $house)
	{
		$this->name = $name;
		$this->surname = $surname;
		$this->car = $car;
		$this->address = $address;
		$this->country = $country;
		$this->house = $house;
	}
}