<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class InvoiceRepository extends EntityRepository
{
    /**
     * @param Ticket $ticket
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getActualInvoice(Ticket $ticket)
    {
        $qb = $this->createQueryBuilder('i');
        $query = $qb->where('i.ticket = :ticket')
                ->setParameter('ticket', $ticket)
            ->andWhere('i.status IN (:new, :partial_paid)')
                ->setParameter('new', Invoice::STATUS_NEW)
                ->setParameter('partial_paid', Invoice::STATUS_PARTIAL_PAID)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}