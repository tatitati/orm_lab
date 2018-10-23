<?php
Namespace App\Entity\PersistenceModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="house")
 */
class House
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", name="rooms_amount") **/
    private $roomsAmount;

    /**
     * We don't need any setter for this bidirectinal property. Is set by
     * Doctrine using reflection. We can find users, and in the result
     * will be filled with books and each book will reference a list of users
     *
     *
     * BIDIRECTIONAL - INVERSE SIDE
	 *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PersistenceModel\User", mappedBy="book", cascade={"persist", "remove" })
     */
    private $users;

    public function __construct(int $roomsAmount)
    {
        $this->roomsAmount = $roomsAmount;
        $this->users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoomsAmount(): int
    {
        return $this->roomsAmount;
    }

	/**
	 * @return ArrayCollection
	 */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user)
    {
		$this->users->add($user);
		return $this;
    }

    public function removeUser(User $user)
    {
    	$this->users->remove($user);
    	return $this;
    }

}