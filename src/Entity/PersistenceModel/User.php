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
     * I DONT NEED TO ADD inversedBy IN HERE AS I'M NOT DOING BIDIRECTIONAL RELATIONSHIPS. AS YOU
     * CAN SEE I DIDN'T ADD ANY OneToMany SPEC IN THE Car entity. This last is only needed if you want a
     * bidirectional relationship.
     *
     * @var Car
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\Car", cascade={"persist", "remove" })
     */
    private $car;

    /**
     * @var Book
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\Book", cascade={"persist", "remove" }, inversedBy="users")
     */
    private $book;

    /**
     * Custom mapping type
     * Example of database value: Madrid,23NRR,McShit Square
     * When reading from database, this is converted into an object Address
     *
     * @ORM\Column(type="address", name="address") *
     */
    private $address;


    public function __construct(string $name, Car $car, Address $address, Book $book = null) // because in the constructor $car is a mandatory value, this means that is not nullable in db
    {
        $this->name = $name;
        $this->car = $car;
        $this->book = $book;
        $this->address = $address;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function getBook(): Book
    {
        return $this->book;
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
