<?php
Namespace App\Entity\PersistenceModel;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\Car", inversedBy="user", cascade={"persist", "remove" })
     */
    private $car;

    public function __construct(string $name, Car $car) // because in the constructor $car is a mandatory value, this means that is not nullable in db
    {
        $this->name = $name;
        $this->car = $car;
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
}
