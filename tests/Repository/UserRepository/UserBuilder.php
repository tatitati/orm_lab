<?php

namespace App\Tests\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\CustomMappingTypes\CountryId;
use App\Entity\PersistenceModel\Car;
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

	/** @var CountryId */
	private $countryId;

	/** @var House */
	private $house;


	public static function any()
	{
		$name = 'Francisco';
		$surname = 'surname1 surname2';
		$car = new Car('Renault', 'black');
		$address = new Address('Madrid', '23NRR', 'McShit Square');
		//$countryId = new CountryId(23);
		$house = null;

		return new self($name, $surname, $car, $address, $house);

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

	public function withCountryId(int $id)
	{
		$this->counttryId = new CountryId($id);
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
			//$this->countryId,
			$this->house
		);
	}

	private function __construct(string $name, string $surname, Car $car, Address $address, ?House $house)
	{
		$this->name = $name;
		$this->surname = $surname;
		$this->car = $car;
		$this->address = $address;
		//$this->countryId = $countryId;
		$this->house = $house;
	}
}