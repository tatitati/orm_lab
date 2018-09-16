<?php
namespace App\Repository;

use App\Entity\PersistenceModel\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

Class UserRepositoryDB extends EntityRepository
{
    private $em;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function save(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user->id();
    }

    public function findAll()
    {
        // TODO: Implement findAll() method.
    }
}
