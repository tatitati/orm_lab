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

    public function testTypeRepository()
	{
		$this->assertInstanceOf(EntityRepository::class, $this->bookRepository);

		$this->assertThat($this->userRepository,
			$this->logicalAnd(
				$this->isInstanceOf(EntityRepository::class),
				$this->isInstanceOf(UserRepositoryDB::class)
			)
		);
	}

    /**
     * @test
     */
    public function findOneBy_return_an_entity()
    {
        $this->userRepository->save($this->user());

        $user = $this->userRepository->findOneBy(['name' => 'Francisco']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceof(Car::class, $user->getCar());
    }

    /**
     * @test
     */
    public function findAll_return_an_array_with_entities()
    {
        $this->userRepository->save($this->user());

        /** @var User $user */
        $users = $this->userRepository->findAll();

        $this->assertInternalType('array', $users);
        $this->assertContainsOnlyInstancesOf(User::class, $users);
    }

	/**
	 * @test
	 */
    public function many_to_one_relations_are_loaded_as_entity()
    {
	    $book = new Book('title1', 'category1');
	    $user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
	    $user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);

	    $book->addUser($user1)// modify these books also modify the book passed to User1 and User2 as object are reference types
	        ->addUser($user2);

	    $this->userRepository
		    ->save($user1)
		    ->save($user2);

	    /** @var User $user */
	    $user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

	    $this->assertInstanceOf(Book::class, $user->getBook());
    }

	/**
	 * @test
	 */
	public function one_to_many_relations_are_loaded_as_collection()
	{
		$book = new Book('title1', 'category1');
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);

		$book->addUser($user1)// modify these books also modify the book passed to User1 and User2 as object are reference types
		->addUser($user2);

		$this->userRepository
			->save($user1)
			->save($user2);

		/** @var Book $book */
		$book = $this->bookRepository->findOneBy(['title' => 'title1']);

		$this->assertInstanceOf(Collection::class, $book->getUsers());
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

        $this->userRepository
	        ->save($user1)
            ->save($user2);

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

        // Bidirectional relationships
        $this->assertInstanceOf(Collection::class, $user->getBook()->getUsers());
        $this->assertEquals($user1Name, $user->getBook()->getUsers()[0]->getName());
        $this->assertEquals($user2Name, $user->getBook()->getUsers()[1]->getName());

        // Circular references very visual in here
        $this->assertInstanceOf(Book::class, $user->getBook()->getUsers()[0]->getBook()->getUsers()[0]->getBook());
    }

	/**
	 * @test
	 */
	public function can_get_relations_bidirectionals_searching_also_by_book()
	{
		$book1 = new Book('title1', 'category1');
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book1);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book1);

		$book1->addUser($user1)// modify these books also modify the book passed to User1 and User2 as object are reference types
			->addUser($user2);

		$this->userRepository
			->save($user1)
			->save($user2);

		/** @var Book $bookResult */
		$book = $this->bookRepository->findOneBy(['title' => 'title1']);

		// bidirectional relations
		$this->assertEquals($user1Name, $book->getUsers()[0]->getName());
		$this->assertEquals($user2Name, $book->getUsers()[1]->getName());

		// Circular references
		$this->assertEquals($book1, $book->getUsers()[0]->getBook()->getUsers()[0]->getBook());
		$this->assertInstanceOf(Book::class, $book->getUsers()[0]->getBook()->getUsers()[0]->getBook());
	}

	/**
	 * @test
	 */
	public function a_collection_empty_is_also_returned()
	{
		$book1 = new Book('title1', 'category1');
		$user1 = $this->user('user_with_car_and_book_ONE', $book1);
		$user2 = $this->user('user_with_car_and_book_TWO', $book1);

		//	$book->addUser($user1)
		//	    ->addUser($user2);

		$this->userRepository
			->save($user1)
			->save($user2);

		/** @var Book $result */
		$book = $this->bookRepository->findOneBy(['title' => 'title1']);

		// basic relations
		$this->assertInstanceOf(Book::class, $book);
		$this->assertInstanceOf(Collection::class, $book->getUsers());
		$this->assertTrue($book->getUsers()->isEmpty());
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
