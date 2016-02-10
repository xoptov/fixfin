<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{
    /**
     * @param int $limit
     * @return News[]
     */
    public function getLastNews($limit)
    {
        $qb = $this->createQueryBuilder('n');
        $query = $qb->where('n.enabled = :enabled')
                ->setParameter('enabled', true)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }
}
