<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MoneyTransactionRepository extends EntityRepository
{
    /**
     * @return MoneyTransaction[]
     */
    public function getScheduledRewinds()
    {
        $qb = $this->createQueryBuilder('mt');
        $query = $qb->where('mt.status = :status_new')
                ->setParameter('status_new', MoneyTransaction::STATUS_NEW)
            ->andWhere('mt.type = :type_reward')
                ->setParameter('type_reward', MoneyTransaction::TYPE_REWARD)
            ->orderBy('mt.createdAt', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}