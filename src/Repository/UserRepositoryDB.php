<?php
namespace App\Repository;

use App\Entity\PersistenceModel\User;
use Doctrine\ORM\EntityRepository;

Class UserRepositoryDB extends EntityRepository implements UserRepository
{
    public function save(User $user)
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
}
