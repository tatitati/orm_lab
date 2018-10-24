<?php
Namespace Tests\App\Repository\UserRepository;

use App\Entity\CustomMappingTypes\Address;
use App\Entity\PersistenceModel\Book;
use App\Entity\PersistenceModel\Car;
use App\Entity\PersistenceModel\User;
use App\Repository\UserRepositoryDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HydratationsTest extends KernelTestCase
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
    public function when_hydratation_is_array_then_keys_are_the_same_entity_field_names()
    {
        $this->userRepository->save($this->user());
        $qb = $this->em->createQueryBuilder();
        $query = $qb->select(['u'])
            ->from('App\Entity\PersistenceModel\User', 'u')
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->getArrayResult();

        $this->assertInstanceOf(Address::class, $result[0]['address']);
        $this->assertThat($result[0],
            $this->logicalAnd(
                $this->arrayHasKey('id'),
                $this->arrayHasKey('name'),
                $this->arrayHasKey('surName')
        ));
    }

    /**
     * @test
     */
    public function test_when_hydratation_is_scalar()
    {
        $this->userRepository->save($this->user());
        $qb = $this->em->createQueryBuilder();
        $query = $qb->select(['u'])
            ->from('App\Entity\PersistenceModel\User', 'u')
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->getResult(Query::HYDRATE_SCALAR);

        $this->assertInstanceOf(Address::class, $result[0]['u_address']);
        $this->assertThat($result[0],
            $this->logicalAnd(
                $this->arrayHasKey('u_id'),
                $this->arrayHasKey('u_name'),
                $this->arrayHasKey('u_surName')
            ));
    }


    protected function tearDown()
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();

        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    private function user()
    {
        return new User(
            'Francisco',
            'surname1 surname2',
            new Car('Renault', 'black'),
            new Address(
                'Madrid',
                '23NRR',
                'McShit Square'
            )
        );
    }
}
