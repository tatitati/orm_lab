<?php
Namespace App\Entity\PersistenceModel;

use App\Entity\CustomMappingTypes\Address;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepositoryDB")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", name="name") **/
    private $name;

	/**
	 * Custom mapping type
	 * Example of database value: Madrid,23NRR,McShit Square
	 * When reading from database, this is converted into an object Address
	 *
	 *
	 * @ORM\Column(type="address", name="address")
	 */
	private $address;

    /**
     * The column mame by default in database will be sur_name, but when hydrated as array, it will be 'surName' key
     * 
     * @ORM\Column(type="string")
     */
    private $surName;

    /**
     * I DONT NEED TO ADD inversedBy IN HERE AS I'M NOT DOING BIDIRECTIONAL RELATIONSHIPS. AS YOU
     * CAN SEE I DIDN'T ADD ANY OneToMany SPEC IN THE Car entity. This last is only needed if you want a
     * bidirectional relationship.
     *
     *
     * UNIDIRECTIONAL
     *
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\Car", cascade={"persist", "remove" })
     */
    private $car;

    /**
     * BIDIRECTIONAL - OWNING SIDE
     *
     * @var House
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\House", cascade={"persist", "remove" }, inversedBy="users")
     */
    private $house;

	/**
	 * WE WANT TO REFERENCE OTHER AGGREGATES BY ID. So we want to force a foreign key but at the same time avoid a relation
	 *
	 * @var Country
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\Country", cascade={"persist", "remove"})
	 */
    private $country;

    public function __construct(string $name, string $surname, Car $car, Address $address, Country $country, House $house = null) // because in the constructor $car is a mandatory value, this means that is not nullable in db
    {
        $this->name = $name;
        $this->surName = $surname;
        $this->car = $car;
        $this->address = $address;
        $this->country = $country;
        $this->house = $house;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurName(): string
    {
        return $this->surName;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function getHouse(): ?House
    {
        return $this->house;
    }

    public function setHouse(House $house)
    {
    	$this->house = $house;
    	// set bidirectionals relationship
    	$house->addUser($this);
    	return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getCountry(): Country
	{
		return $this->country;
	}

    public function getCountryId(): int {
    	// lazy relation, it only loads the id without any secondary request to db
    	return $this->country->getId();
	}

    public function setCar()
    {
        // when reading data this is never called!!!!!!, Reflection bypass setters and constructors, and set values directly into the variables
        echo 'asdfasdfasdfadsf';
        die('asdfasdfasdfadsfasdfasdf');
    }
}
