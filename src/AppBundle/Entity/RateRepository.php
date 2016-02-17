<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class RateRepository extends EntityRepository
{
    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCheapestRate()
    {
        $qb = $this->createQueryBuilder('r');
        $query = $qb->orderBy('r.amount', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }
}