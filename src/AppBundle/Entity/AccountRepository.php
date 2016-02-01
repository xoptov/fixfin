<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{
    /**
     * @param Pool $pool
     * @throws EntityNotFoundException
     */
    public function getPoorestSystemAccount(Pool $pool)
    {
        throw new EntityNotFoundException("Взвана заглушка");
    }

    /**
     * @param Pool $pool
     * @throws EntityNotFoundException
     */
    public function getWealthSystemAccount(Pool $pool)
    {
        throw new EntityNotFoundException("Вызвана заглушка");
    }
}