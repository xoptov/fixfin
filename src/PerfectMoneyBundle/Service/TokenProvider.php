<?php

namespace PerfectMoneyBundle\Service;

use AppBundle\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class TokenProvider implements TokenProviderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getToken($number)
    {
        $account = $this->entityManager->getRepository('AppBundle:Account')
            ->findOneBy(['number' => $number]);

        if (!$account instanceof Account) {
            throw new NoResultException();
        }

        return md5($account->getPassPhrase());
    }
}