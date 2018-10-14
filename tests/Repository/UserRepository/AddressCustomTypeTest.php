<?php
Namespace Tests\App\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function when_reading_addres_type_is_mapped_properly()
    {
        $this->userRepository->save($this->user());

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
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    private function user()
    {
        return new User(
            'Francisco',
            new Car('Renault', 'black'),
            new Address(
                'Madrid',
                '23NRR',
                'McShit Square'
            )
        );
    }
}
