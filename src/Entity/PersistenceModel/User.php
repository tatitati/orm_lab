<?php
Namespace App\Entity\PersistenceModel;

use Doctrine\Common\Collections\ArrayCollection;
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

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->car = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|Car[]
     */
    public function getCar()
    {
        return $this->car;
    }

    public function setCar(Car $car)
    {
        $this->car = $car;
    }
}
