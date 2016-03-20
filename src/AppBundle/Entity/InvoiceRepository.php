<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class InvoiceRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param Rate $rate
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getActualInvoice(User $user, Rate $rate)
    {
        $qb = $this->createQueryBuilder('i');
        $query = $qb->where('i.user = :user')
                ->setParameter('user', $user)
            ->andWhere('i.rate = :rate')
                ->setParameter('rate', $rate)
            ->andWhere('i.status IN (:new, :partial_paid)')
                ->setParameter('new', Invoice::STATUS_NEW)
                ->setParameter('partial_paid', Invoice::STATUS_PARTIAL_PAID)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}