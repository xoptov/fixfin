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
                ->setParameter('expired', false);

        $directReferrer = $ticket->getUser()->getReferrer();

        if ($directReferrer instanceof User) {
            $qb->andWhere('materialized_path_entity != :direct_referrer')
                ->setParameter('direct_referrer', $directReferrer);
        }

        return $qb->getQuery()->getResult();
    }
}