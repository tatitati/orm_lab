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
     * @var Book
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\House", cascade={"persist", "remove" }, inversedBy="users")
     */
    private $house;

    /**
     * Custom mapping type
     * Example of database value: Madrid,23NRR,McShit Square
     * When reading from database, this is converted into an object Address
     *
     *
     * @ORM\Column(type="address", name="address")
     */
    private $address;


    public function __construct(string $name, string $surname, Car $car, Address $address, House $house = null) // because in the constructor $car is a mandatory value, this means that is not nullable in db
    {
        $this->name = $name;
        $this->surName = $surname;
        $this->car = $car;
        $this->house = $house;
        $this->address = $address;
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
    	$house->addUser($this);
    	return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setCar()
    {
        // when reading data this is never called!!!!!!, Reflection bypass setters and constructors, and set values directly into the variables
        echo 'asdfasdfasdfadsf';
        die('asdfasdfasdfadsfasdfasdf');
    }
}
