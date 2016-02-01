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
    public function getReferrersBranch(Ticket $ticket)
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

    /**
     * @param Ticket $ticket
     * @return QueryBuilder
     */
    public function getReferralsQueryBuilder(Ticket $ticket)
    {
        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.referrals', 'r')
            ->innerJoin('r.tickets', 'rt')
            ->where('rt.chiefTicket != :ticket')
                ->setParameter('ticket', $ticket)
            ->andWhere('rt.rate = :rate')
                ->setParameter('rate', $ticket->getRate());

        return $qb;
    }

    /**
     * @param Ticket $ticket
     * @return User[]
     */
    public function getReferrals(Ticket $ticket)
    {
        $qb = $this->getReferralsQueryBuilder($ticket);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param Ticket $ticket
     * @return User[]
     */
    public function getReferralsExcludeQualification(Ticket $ticket)
    {
        $qb = $this->getReferralsQueryBuilder($ticket);
        $query = $qb->andWhere($qb->expr()->notIn('rt', $ticket->getQualification()->getTransferredTickets()))
            ->getQuery();

        return $query->getResult();
    }
}