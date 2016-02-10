<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class AccountRepository extends EntityRepository
{
    public function getPoolSystemAccountQueryBuilder(Pool $pool)
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPoorestSystemAccount(Pool $pool)
    {
        $qb = $this->getPoolSystemAccountQueryBuilder($pool)
            ->orderBy('a.balance', 'ASC');

        $query = $qb->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param Pool $pool
     * @return Account
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getWealthSystemAccount(Pool $pool)
    {
        $qb = $this->getPoolSystemAccountQueryBuilder($pool)
            ->orderBy('a.balance', 'DESC');

        $query = $qb->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param $number
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getAccountByNumber($number)
    {
        $qb = $this->createQueryBuilder('a');
        $query = $qb->where('a.number = :number')
            ->setParameter('number', $number)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param Account $account
     * @return float
     */
    public function getReservedAmount(Account $account)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb->select('SUM(mt.amount)')
            ->from('AppBundle:Account', 'a')
            ->innerJoin('a.outcomes', 'mt')
            ->where('a = :account')
                ->setParameter('account', $account)
            ->andWhere('mt.type = :type')
                ->setParameter('type', MoneyTransaction::TYPE_REWARD)
            ->andWhere('mt.status IN (:statuses)')
                ->setParameter('statuses', array(MoneyTransaction::STATUS_NEW, MoneyTransaction::STATUS_RETRY))
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}