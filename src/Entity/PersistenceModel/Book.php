<?php
Namespace App\Entity\PersistenceModel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="book")
 */
class Book
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", name="title") **/
    private $title;

    /** @ORM\Column(type="string", name="category") **/
    private $category;

    /**
     * We don't need any setter for this bidirectinal property. Is set by Doctrine using reflection. We can find users, and in the result
     * will be filled with books and each book will reference a list of users
     *
     * @var User[]
     * @ORM\OneToMany(targetEntity="App\Entity\PersistenceModel\User", mappedBy="book")
     */
    private $users;

    public function __construct(string $title, string $category)
    {
        $this->title = $title;
        $this->category = $category;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getUsers()
    {
        return $this->users;
    }
}