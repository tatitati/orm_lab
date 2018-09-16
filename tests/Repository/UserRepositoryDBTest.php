<?php

use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryDBTest extends KernelTestCase
{
    /** @var UserRepositoryDB */
    private $repository;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $this->repository = $em->getRepository(User::class);
    }

    public function testSave()
    {
        $user = new User('Francisco');

        $userId = $this->repository->save($user);

        $this->assertInternalType('integer', $userId);
    }
}
