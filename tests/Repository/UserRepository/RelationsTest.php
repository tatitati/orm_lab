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

    //
	// Repository types
	//

    public function testTypeRepository()
	{
		$this->assertInstanceOf(EntityRepository::class, $this->bookRepository);

		$this->assertThat($this->userRepository,
			$this->logicalAnd(
				// we didn't define any bookRepository, so we have a generic one EntityRepository
				// this is dangerous as this allows to any user to fetch individual entity domains instead of
				// consistent aggregates as a whole
				$this->isInstanceOf(EntityRepository::class),
				$this->isInstanceOf(UserRepositoryDB::class)
			)
		);
	}

	//
	// results Type from findOneBy and findAll
	//

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
    public function findAll_return_an_array_of_entities()
    {
        $this->userRepository->save($this->user());

        /** @var User $user */
        $users = $this->userRepository->findAll();

        $this->assertThat($users,
	        $this->logicalAnd(
				$this->isType('array'),
		        $this->containsOnlyInstancesOf(User::class)
			)
        );
    }

	//
	// collections EMPTY and NO EMPTY
	//

	/**
	 * @test
	 */
    public function in_many_to_one_the_one_side_is_loaded_as_entity()
    {
	    $book = new Book('title1', 'category1');
	    $user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
	    $user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);
	    $this->userRepository->save($user1)->save($user2);

	    /** @var User $user */
	    $user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

	    $this->assertInstanceOf(Book::class, $user->getBook());
    }

	/**
	 * @test
	 */
	public function in_one_to_many_the_many_side_is_loaded_as_collection_empty()
	{
		$book = new Book('title1', 'category1');
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);
		$this->userRepository->save($user1)->save($user2);

		/** @var User $user */
		$book = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE'])->getBook();

		$this->assertInstanceOf(Collection::class, $book->getUsers());
		$this->assertTrue($book->getUsers()->isEmpty());
	}

	/**
	 * @test
	 */
	public function one_to_many_load_entities_as_collections_NO_empty()
	{
		$book = new Book('title1', 'category1');
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);
		$book->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var Book $book */
		$book = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE'])->getBook();

		$this->assertInstanceOf(Collection::class, $book->getUsers());
		$this->assertFalse($book->getUsers()->isEmpty());
	}

	//
	// Graph traversion
	//

	/**
	 * @test
	 */
	public function bidirectionals_graph_traversion_can_start_from_book()
	{
		$book = new Book('title1', 'category1');
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book);
		$book->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var Book $book */
		$book = $this->bookRepository->findOneBy(['title' => 'title1']);

		$this->assertInstanceOf(Collection::class, $book->getUsers());
		$this->assertCount(2, $book->getUsers());
	}


	//
	// circular references
	//

	/**
	 * @test
	 */
	public function circular_references_searching_by_book()
	{
		$book1 = new Book('title1', 'category1');
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $book1);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $book1);
		$book1->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var Book $bookResult */
		$book = $this->bookRepository->findOneBy(['title' => 'title1']);
		
		$this->assertEquals($book1, $book->getUsers()[0]->getBook()->getUsers()[0]->getBook());
		$this->assertInstanceOf(Book::class, $book->getUsers()[0]->getBook()->getUsers()[0]->getBook());
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
