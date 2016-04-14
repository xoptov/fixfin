<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use AppBundle\Entity\Account;
use Doctrine\Common\Persistence\ObjectManager;

class PerfectMoneyNumberToAccountTransformer implements DataTransformerInterface
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ObjectManager $objectManager, LoggerInterface $logger)
    {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    public function transform($value)
    {
        if ($value instanceof Account) {
            return $value->getNumber();
        }

        return '';
    }

    public function reverseTransform($value)
    {
        if (null == $value) {
            return null;
        }

        try {
            return $this->objectManager->getRepository('AppBundle:Account')->getAccountByNumber($value);
        } catch (NoResultException $e) {
            $this->logger->warning('Ошибка трансформирования номера кошелька в системный счёт', ['number' => $value]);
        }

        return null;
    }
}