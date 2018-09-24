<?php
Namespace Tests\App\Repository;

use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Doctrine\Common\Collections\ArrayCollection;
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
        $result = $this->repository->findOneBy(['id' => 1]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertInstanceof(Car::class, $result->getCar());
    }

    /**
     * @test
     */
    public function multiple_rows_return_an_array()
    {
        $user = new User('Francisco', $car = new Car('Renault', 'black'));

        $this->repository->save($user);

        /** @var User $user */
        $result = $this->repository->findAll();

        $this->assertInternalType('array', $result);
        $this->assertContainsOnlyInstancesOf(User::class, $result);
    }

    /**
     * @test
     */
    public function bidirectional_relations()
    {
        $book = new Book('title1', 'category1');
        $user1 = new User(
            'user_with_car_and_book_ONE',
            new Car('Renault', 'black'),
            $book
        );

        $user2 = new User(
            'user_with_car_and_book_TWO',
            new Car('Renault', 'black'),
            $book
        );

        $this->repository->save($user1);
        $this->repository->save($user2);

        /** @var User $user */
        $result = $this->repository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertInstanceOf(Car::class, $result->getCar());
        $this->assertInstanceOf(Book::class, $result->getBook());
        // HERE WE CAN SEE THE BIDIRECTIONAL RELATION WORKING
        $this->assertEquals('user_with_car_and_book_ONE', $result->getBook()->getUsers()[0]->getName());
        $this->assertEquals('user_with_car_and_book_TWO', $result->getBook()->getUsers()[1]->getName());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
