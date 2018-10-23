<?php
Namespace Tests\App\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RelationsTest extends KernelTestCase
{
    private $em;

    /** @var UserRepositoryDB */
    private $userRepository;

	/** @var  Doctrine\ORM\EntityRepository */
	private $bookRepository;

	public function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(User::class);
        // we didn't define any bookRepository, so we have a generic one EntityRepository
	    $this->bookRepository = $this->em->getRepository(Book::class);
    }

    public function testGenericTypeRepository()
	{
		$this->assertInstanceOf(EntityRepository::class, $this->bookRepository);

		$this->assertInstanceOf(EntityRepository::class, $this->userRepository);
		$this->assertInstanceOf(UserRepositoryDB::class, $this->userRepository);
	}

    /**
     * @test
     */
    public function on_reading_user_also_load_referenced_entities_like_car()
    {
        $this->userRepository->save($this->user());

        $result = $this->userRepository->findOneBy(['name' => 'Francisco']);

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

        $user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
        $user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);

        $book->addUser($user1)// modify these books also modify the book passed to User1 and User2 as object are reference types
	        ->addUser($user2);

        $this->userRepository->save($user1);
        $this->userRepository->save($user2);

        /** @var User $user */
        $result = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

	    // basic relations
        $this->assertInstanceOf(User::class, $result);
        $this->assertInstanceOf(Car::class, $result->getCar());
        $this->assertInstanceOf(Book::class, $result->getBook());

        // Bidirectional relationships
        $this->assertInstanceOf(Collection::class, $result->getBook()->getUsers());
        $this->assertEquals($user1Name, $result->getBook()->getUsers()[0]->getName());
        $this->assertEquals($user2Name, $result->getBook()->getUsers()[1]->getName());

        // Circular references very visual in here
        $this->assertInstanceOf(Book::class, $result->getBook()->getUsers()[0]->getBook()->getUsers()[0]->getBook());
    }

	/**
	 * @test
	 */
	public function can_get_relations_bidirectionals_searching_also_by_book()
	{
		$book = new Book('title1', 'category1');

		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);

		$book->addUser($user1)// modify these books also modify the book passed to User1 and User2 as object are reference types
			->addUser($user2);

		$this->userRepository->save($user1);
		$this->userRepository->save($user2);

		/** @var Book $result */
		$result = $this->bookRepository->findOneBy(['title' => 'title1']);

		// basic relations
		$this->assertInstanceOf(Book::class, $result);
		$this->assertInstanceOf(Collection::class, $result->getUsers());

		// bidirectional relations
		$this->assertEquals($user1Name, $result->getUsers()[0]->getName());
		$this->assertEquals($user2Name, $result->getUsers()[1]->getName());

		// Circular references
		$this->assertEquals($book, $result->getUsers()[0]->getBook()->getUsers()[0]->getBook());
		$this->assertInstanceOf(Book::class, $result->getUsers()[0]->getBook()->getUsers()[0]->getBook());
	}

	/**
	 * @test
	 */
	public function a_collection_empty_is_also_returned()
	{
		$book = new Book('title1', 'category1');

		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);

		//	$book->addUser($user1)
		//	    ->addUser($user2);

		$this->userRepository->save($user1);
		$this->userRepository->save($user2);

		/** @var Book $result */
		$result = $this->bookRepository->findOneBy(['title' => 'title1']);

		// basic relations
		$this->assertInstanceOf(Book::class, $result);
		$this->assertInstanceOf(Collection::class, $result->getUsers());
		$this->assertTrue($result->getUsers()->isEmpty());
	}

    protected function tearDown()
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();

        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    private function user(string $name = 'Francisco', $book = null)
    {
        return new User(
            $name,
            'surname1 surname2',
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
