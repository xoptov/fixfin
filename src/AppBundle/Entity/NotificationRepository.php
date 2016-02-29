<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param int $limit
     * @return array
     */
    public function getLastUnread(User $user, $limit)
    {
        $qb = $this->createQueryBuilder('n');
        $query = $qb->where('n.user = :user')
                ->setParameter('user', $user)
            ->andWhere('n.viewed = :viewed')
                ->setParameter('viewed', false)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }
}