<?php
Namespace Tests\App\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\House;
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
	private $houseRepository;

	public function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(User::class);
        // we didn't define any bookRepository, so we have a generic one EntityRepository
	    $this->houseRepository = $this->em->getRepository(House::class);
    }

    //
	// Repository types
	//

    public function testTypeRepository()
	{
		$this->assertInstanceOf(EntityRepository::class, $this->houseRepository);

		$this->assertThat($this->userRepository,
			$this->logicalAnd(
				// we didn't define any houseRepository class, so we have a generic one EntityRepository.
				// This is dangerous as this allows to any user to fetch individual entity domains instead of
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
	    $house = new House(34);
	    $user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $house);
	    $user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $house);
	    $this->userRepository->save($user1)->save($user2);

	    /** @var User $user */
	    $user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

	    $this->assertInstanceOf(House::class, $user->getHouse());
    }

	/**
	 * @test
	 */
	public function many_side_is_always_a_collection()
	{
		$house = new House(34);
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $house);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $house);
		$this->userRepository->save($user1)->save($user2);

		/** @var House $house */
		$user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

		$this->assertInstanceOf(
			Collection::class, $user->getHouse()->getUsers(),
			'Many sides are always a collection'
		);
	}

	/**
	 * @test 
	 */
	public function in_one_to_many_the_many_side_is_loaded_as_collection_empty()
	{
		$house = new House(34);
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $house);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $house);
		//$house1->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var House $house */
		$user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

		$this->assertTrue(
			$user->getHouse()->getUsers()->isEmpty(),
			'Is empty as any bidirectional relation need updated both sides'
		);
	}

	/**
	 * @test
	 */
	public function one_to_many_load_entities_as_collections_NO_empty()
	{
		$house1 = new House(34);
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $house1);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $house1);
		$house1->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var House $house */
		$user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

		$this->assertFalse(
			$user->getHouse()->getUsers()->isEmpty(),
			'Is not empty as both sides of the bidirectional relation are updated'
		);
	}

	/**
	 * @test
	 */
	public function better_bidirectional_association_method()
	{
		$house1 = new House(34);
		// setHouse() update both entities to be bidirectional
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE')->setHouse($house1);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO')->setHouse($house1);
		$this->userRepository->save($user1)->save($user2);

		/** @var House $user */
		$user = $this->userRepository->findOneBy(['name' => 'user_with_car_and_book_ONE']);

		$this->assertThat($user->getHouse()->getUsers(),
			$this->logicalAnd(
				$this->isInstanceOf(Collection::class),
				$this->logicalNot($this->isEmpty())
		));
	}

	//
	// Graph traversion
	//

	/**
	 * @test
	 */
	public function bidirectionals_graph_traversion_can_start_from_house()
	{
		$house1 = new House(34);
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $house1);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $house1);
		$house1->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var House $house */
		$house = $this->houseRepository->findOneBy(['roomsAmount' => 34]);

		$this->assertThat($house->getUsers(),
			$this->logicalAnd(
				$this->isInstanceOf(Collection::class),
				$this->countOf(2)
			));
	}


	//
	// circular references
	//

	/**
	 * @test
	 */
	public function circular_references_searching_by_book()
	{
		$house1 = new House(34);
		$user1 = $this->user($user1Name = 'user_with_car_and_book_ONE', $house1);
		$user2 = $this->user($user2Name = 'user_with_car_and_book_TWO', $house1);
		$house1->addUser($user1)->addUser($user2);
		$this->userRepository->save($user1)->save($user2);

		/** @var House $house */
		$house = $this->houseRepository->findOneBy(['roomsAmount' => 34]);
		
		$this->assertEquals($house1, $house->getUsers()[0]->getHouse()->getUsers()[0]->getHouse());
		$this->assertInstanceOf(House::class, $house->getUsers()[0]->getHouse()->getUsers()[0]->getHouse());
	}

    protected function tearDown()
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();

        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    private function user(string $name = 'Francisco', $house = null)
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
            $house
        );
    }
}
