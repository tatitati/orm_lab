<?php
Namespace Tests\App\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RelationsTest extends KernelTestCase
{
    /** @var UserRepositoryDB */
    private $userRepository;
    private $em;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(User::class);
    }

    /**
     * @test
     */
    public function on_reading_user_also_load_referenced_entities_like_car()
    {
        $this->userRepository->save($this->user());

        $result = $this->userRepository->findOneBy(['id' => 1]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertInstanceof(Car::class, $result->getCar());
    }

    /**
     * @test
     */
    public function on_reading_multiple_rows_return_an_array()
    {
        $this->userRepository->save($this->user());

        /** @var User $user */
        $result = $this->userRepository->findAll();

        $this->assertInternalType('array', $result);
        $this->assertContainsOnlyInstancesOf(User::class, $result);
    }

    /**
     * @test
     */
    public function on_reading_bidirectional_relations()
    {
        $book = new Book('title1', 'category1');
        $user1 = $this->user(
            'user_with_car_and_book_ONE',
            $book
        );

        $user2 = $this->user(
            'user_with_car_and_book_TWO',
            $book
        );

        $this->userRepository->save($user1);
        $this->userRepository->save($user2);

        /** @var User $user */
        $result = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertInstanceOf(Car::class, $result->getCar());
        $this->assertInstanceOf(Book::class, $result->getBook());

        // HERE WE CAN SEE THE BIDIRECTIONAL RELATION WORKING
        $this->assertInstanceOf('Doctrine\ORM\PersistentCollection', $result->getBook()->getUsers());
        $this->assertEquals('user_with_car_and_book_ONE', $result->getBook()->getUsers()[0]->getName());
        $this->assertEquals('user_with_car_and_book_TWO', $result->getBook()->getUsers()[1]->getName());

        // Circular references very visual in here
        $this->assertInstanceOf(Book::class, $result->getBook()->getUsers()[0]->getBook()->getUsers()[0]->getBook());
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    private function user(string $name = 'Francisco', $book = null)
    {
        return new User(
            $name,
            new Car('Renault', 'black'),
            new Address(
                'Madrid',
                '23NRR',
                'McShit Square'
            ),
            $book
        );
    }
}
