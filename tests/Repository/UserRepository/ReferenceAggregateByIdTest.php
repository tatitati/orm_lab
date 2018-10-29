<?php

namespace App\Tests\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReferenceAggregateByIdTest extends KernelTestCase
{
	private $em;

	/** @var UserRepositoryDB */
	private $userRepository;

	/** @var  Doctrine\ORM\EntityRepository */
	private $houseRepository;

//	public function setUp()
//	{
//		$kernel = self::bootKernel();
//		$this->em = $kernel->getContainer()->get('doctrine')->getManager();
//		$this->userRepository = $this->em->getRepository(User::class);
//		// we didn't define any bookRepository, so we have a generic one EntityRepository
//		$this->houseRepository = $this->em->getRepository(House::class);
//	}
}