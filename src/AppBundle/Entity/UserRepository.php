<?php

namespace AppBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

class UserRepository extends MaterializedPathRepository
{
    /**
     * @param Ticket $ticket
     * @return User[]
     */
    public function getReferrers(Ticket $ticket)
    {
        $qb = $this->getPathQueryBuilder($ticket->getUser());

        $qb->innerJoin('materialized_path_entity.tickets', 't')
            ->andWhere('t.rate = :rate')
                ->setParameter('rate', $ticket->getRate())
            ->andWhere('t.expired = :expired')
                ->setParameter('expired', false)
            ->andWhere('materialized_path_entity != :user')
                ->setParameter('user', $ticket->getUser());

        $directReferrer = $ticket->getUser()->getReferrer();

        if ($directReferrer instanceof User) {
            $qb->andWhere('materialized_path_entity != :direct_referrer')
                ->setParameter('direct_referrer', $directReferrer);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $query
     * @return array
     */
    public function searchReferrers($query)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->like('u.username', $qb->expr()->literal($query.'%')))
            ->setMaxResults(20)
        ->orderBy('u.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getLastUsers()
    {
        $qb = $this->createQueryBuilder('u');

        $qb->setMaxResults(20)->orderBy('u.createdAt');

        return $qb->getQuery()->getResult();
    }
}