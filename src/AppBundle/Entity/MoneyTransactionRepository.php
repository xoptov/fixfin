<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

class MoneyTransactionRepository extends EntityRepository
{
    /**
     * @return MoneyTransaction[]|ArrayCollection
     */
    public function getScheduledRewards()
    {
        $qb = $this->createQueryBuilder('mt');
        $query = $qb->where('mt.status IN (:statuses)')
                ->setParameter('statuses', array(MoneyTransaction::STATUS_NEW, MoneyTransaction::STATUS_RETRY))
            ->andWhere('mt.type = :type_reward')
                ->setParameter('type_reward', MoneyTransaction::TYPE_REWARD)
            ->orderBy('mt.createdAt', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}