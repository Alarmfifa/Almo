<?php
namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * TagRepository.
 */
class TagRepository extends ServiceEntityRepository
{

    /**
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     *
     * @param \App\Entity\User $user
     * @param int $type
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getUserTags(\App\Entity\User $user, int $type)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT t FROM App\Entity\Tag t
							WHERE t.userId = :userId and t.payType = :type")
            ->setParameter('userId', $user->getId())
            ->setParameter('type', $type);

        return $query->getResult();
    }

    // TODO что это? почему здесь? какое отношение к тегам?
    /**
     * 
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function findAllOrderByDate()
    {
        return $this->getEntityManager()
            ->createQuery('Select p from Operations p order by p.date desc')
            ->getResult();
    }
}
