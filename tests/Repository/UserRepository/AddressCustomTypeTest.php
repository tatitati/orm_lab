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

class AddressCustomRepository extends KernelTestCase
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
    public function when_reading_address_type_is_mapped_properly()
    {
        $this->userRepository->save(UserBuilder::any()->build());
	    $this->em->clear();

        /** @var User $result */
        $result = $this->userRepository->findOneBy(['name' => 'Francisco']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(
            new Address('Madrid', '23NRR', 'McShit Square'),
            $result->getAddress()
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
}
