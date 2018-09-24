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
     * @ORM\ManyToOne(targetEntity="App\Entity\PersistenceModel\Car", inversedBy="user")
     */
    private $cars;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->cars = new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCars()
    {
        return $this->cars;
    }
}
