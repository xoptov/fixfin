<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Collections\ArrayCollection;

class TicketRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param Rate $rate
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getTicketByRate(Rate $rate, User $user)
    {
        $qb = $this->createQueryBuilder('t');

        $query = $qb->where('t.user = :user')
                ->setParameter('user', $user)
            ->andWhere('t.rate = :rate')
                ->setParameter('rate', $rate)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param Rate $rate
     * @param User[]|ArrayCollection $users
     * @return mixed
     */
    public function getClosestTicketByRate(Rate $rate, $users)
    {
        $qb = $this->createQueryBuilder('t');

        $query = $qb->innerJoin('t.user', 'u')
            ->where('t.rate = :rate')
                ->setParameter('rate', $rate)
            ->andWhere('u IN (:users)')
                ->setParameter('users', $users)
            ->orderBy('u.level', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}