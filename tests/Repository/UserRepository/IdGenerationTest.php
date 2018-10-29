<?php
Namespace Tests\App\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use App\Tests\Repository\UserRepository\UserBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IdGenerationTest extends KernelTestCase
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
    public function on_writing_the_field_id_of_user_is_filled_by_doctrine_automatically()
    {
        // id user is null right now. Persistence ORM will assign an autoincremental id
        $user = $this->user($car = new Car('Renault', 'black'));

        $this->assertNull($user->getId());
        $this->assertNull($car->getId());

        $this->userRepository->save($user);
	    $this->em->clear();

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

    protected function tearDown()
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();

        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    private function user(Car $car)
    {
	    return UserBuilder::any()->withCar($car)->build();
    }

}
