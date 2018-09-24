<?php
Namespace Tests\App\Repository;

use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryDBTest extends KernelTestCase
{
    /** @var UserRepositoryDB */
    private $repository;
    private $em;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();

        $this->repository = $this->em->getRepository(User::class);
    }

    /**
     * @test
     */
    public function on_saving_the_field_id_of_user_is_filled_by_doctrine_automatically()
    {
        // id user is null right now. Persistence ORM will assign an autoincremental id
        $user = new User('Francisco', $car = new Car('Renault', 'black'));

        $this->assertNull($user->getId());
        $this->assertNull($car->getId());

        $this->repository->save($user);

        // If we are creating a new User, the id is automatically populated even if the
        // property is private (using reflection) and there is no setter for it.
        $this->assertThat(
            $user->getId(),
            $this->logicalAnd(
                $this->greaterThan(0), $this->isType('integer')
            )
        );

        $this->assertThat(
            $car->getId(),
            $this->logicalAnd(
                $this->greaterThan(0), $this->isType('integer')
            )
        );
    }

    /**
     * @test
     */
    public function when_reading_user_also_load_referenced_entities_like_car()
    {
        $user = new User('Francisco', $car = new Car('Renault', 'black'));

        $this->repository->save($user);

        /** @var User $user */
        $user = $this->repository->findOneBy(['id' => 1]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceof(Car::class, $user->getCar());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
