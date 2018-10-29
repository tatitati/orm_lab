<?php

namespace App\Tests\Repository\UserRepository;

use App\Entity\CustomMappingTypes\CountryId;
use App\Entity\PersistenceModel\Country;
use App\Entity\PersistenceModel\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Proxy\Proxy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReferenceAggregateByIdTest extends KernelTestCase
{
	private $em;

	/** @var UserRepositoryDB */
	private $userRepository;

	public function setUp()
	{
		$kernel = self::bootKernel();
		$this->em = $kernel->getContainer()->get('doctrine')->getManager();
		$this->userRepository = $this->em->getRepository(User::class);
	}

	public function we_can_reference_to_a_different_aggregate_knowing_how_lazy_calls_work_and_controlling_access_in_entities()
	{
		$this->userRepository->save(UserBuilder::any()->build());
		$this->em->clear();

		$user = $this->userRepository->findOneBy(['name' => 'Francisco']);


		$this->assertInstanceOf(Proxy::class, $user->getCountry(), 'Until we dont request an field different of id(), we will get a proxy');
		$this->assertThat(
			$user->getCountryId(),
				$this->logicalAnd(
					$this->logicalNot($this->isEmpty()),
					$this->isType('int')
		));


		$user->getCountry()->getName();
		$this->assertInstanceOf(Country::class, $user->getCountry(), 'After request a different field of ID, then the Proxy becomes a real entity');
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