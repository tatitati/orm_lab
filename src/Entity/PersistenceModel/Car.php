<?php
namespace App\Entity\PersistenceModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * THIS INDIVIDUAL ENTITY DOESNT SPECIFY A REPOSITORY CLASS AS IT IS JUST AN INDIVIDUAL ENTITY, BUT NOT AN AGREGATE. I
 * DON'T WANT A REPOSITORY FOR THIS ENTITY TO AVOID ANYONE FETCHING THIS LIKE STAND-ALONE
 *
 * @ORM\Entity
 * @ORM\Table(name="car")
 */
class Car
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", name="brand") **/
    private $brand;

    /** @ORM\Column(type="string", name="color") **/
    private $color;

    // User->car is a unidirectional relation. So this doesn't make any reference to User
    public function __construct(string $brand, string $color)
    {
        $this->brand = $brand;
        $this->color = $color;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function getColor()
    {
        return $this->color;
    }
}
