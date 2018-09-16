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
}
