<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{
    public function getSystemAccountQueryBuilder(Pool $pool)
    {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.pools', 'p')
            ->where('p = :pool')
                ->setParameter('pool', $pool)
            ->andWhere('a.balance IS NOT NULL')
            ->setMaxResults(1);

        return $qb;
    }

    /**
     * @param Pool $pool
     * @return Account
     */
    public function getPoorestSystemAccount(Pool $pool)
    {
        $qb = $this->getSystemAccountQueryBuilder($pool)
            ->orderBy('a.balance', 'ASC');

        $query = $qb->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param Pool $pool
     * @return Account
     */
    public function getWealthSystemAccount(Pool $pool)
    {
        $qb = $this->getSystemAccountQueryBuilder($pool)
            ->orderBy('a.balance', 'DESC');

        $query = $qb->getQuery();

        return $query->getSingleResult();
    }
}