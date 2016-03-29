<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

class TicketRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param Rate $rate
     * @return Ticket|null
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
     * @param Ticket $ticket
     * @param User[]|ArrayCollection $users
     * @return Ticket|null
     * @throws NonUniqueResultException
     */
    public function getClosestTicket(Ticket $ticket, $users)
    {
        $qb = $this->createQueryBuilder('t');

        $qb->innerJoin('t.user', 'u')
            ->where('t.rate = :rate')
                ->setParameter('rate', $ticket->getRate())
            ->andWhere('t.expired = :not_expired')
                ->setParameter('not_expired', false)
            ->andWhere('u IN (:users)')
                ->setParameter('users', $users);

        if ($ticket->getChiefTicket() instanceof Ticket) {
            $qb->andWhere('t != :current')
                ->setParameter('current', $ticket->getChiefTicket());
        }

        $query = $qb->orderBy('u.level', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param Ticket $ticket
     * @return QueryBuilder
     */
    public function getReferralsTicketsQueryBuilder(Ticket $ticket)
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.user', 'u')
            ->where('u.referrer = :referrer')
                ->setParameter('referrer', $ticket->getUser())
            ->andWhere('t.rate = :rate')
                ->setParameter('rate', $ticket->getRate());

        return $qb;
    }

    /**
     * @param Ticket $ticket
     * @return Ticket[]|null
     */
    public function getReferralsTickets(Ticket $ticket)
    {
        $qb = $this->getReferralsTicketsQueryBuilder($ticket);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Ticket $ticket
     * @param bool $excludeTransferred
     * @return Ticket[]|null
     */
    public function getLostReferralsTickets(Ticket $ticket, $excludeTransferred = true)
    {
        $qb = $this->getReferralsTicketsQueryBuilder($ticket);
        $qb->andWhere('(t.chiefTicket != :ticket OR t.chiefTicket IS NULL)')
            ->setParameter('ticket', $ticket);

        if ($excludeTransferred && $ticket->getQualification()) {
            $qualification = $ticket->getQualification();
            if (count($qualification->getTransferredTickets())) {
                $qb->andWhere('t NOT IN (:transferred_tickets)')
                    ->setParameter('transferred_tickets', $qualification->getTransferredTickets());
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Rate $rate
     * @param User $referrer
     * @return Ticket[]|null
     */
    public function getReferralsTicketsByRate(Rate $rate, User $referrer)
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb->innerJoin('t.chiefTicket', 'ct')
            ->where('t.rate = :rate')
                ->setParameter('rate', $rate)
            ->andWhere('ct.user = :user')
                ->setParameter('user', $referrer)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return array
     */
    public function getWithExpiredDate()
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb->where('t.expired = :expired')
                ->setParameter('expired', false)
            ->andWhere('t.type = :type')
                ->setParameter('type', Ticket::TYPE_SUBSCRIPTION)
            ->andWhere('t.paidUp <= :now')
                ->setParameter('now', new \DateTime())
            ->getQuery();

        return $query->getResult();
    }
}