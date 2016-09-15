<?php

namespace Almo\WalletBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * OperationsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperationsRepository extends EntityRepository
{
	
	public function findAllOrderByDate()
	{
		return $this->getEntityManager()
			->createQuery('Select p from AlmoWalletBundle:Operations p order by p.date desc')
			->getResult();
	}
	
}