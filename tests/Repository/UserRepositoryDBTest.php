<?php
Namespace Tests\App\Repository;

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
        // id user is null right now. Persistence ORM will assign an autoincremental id
        $user = new User('Francisco');

        $userId = $this->repository->save($user);

        // If we are creating a new User, the id is automatically populated even if the
        // property is private (using reflection) and there is no setter for it.
        $this->assertInternalType('integer', $userId);
        $this->assertTrue($userId !== 0);
    }

    public function testRead()
    {
        $user1 = new User('user1');
        $user2 = new User('user2');
        $user3 = new User('user3');


        $this->repository->save($user1);
        $this->repository->save($user2);
        $this->repository->save($user3);

        $this->assertCount(3, $this->repository->findAll());
    }
}
