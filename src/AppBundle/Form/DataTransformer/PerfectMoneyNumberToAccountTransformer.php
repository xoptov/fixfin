<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use AppBundle\Entity\Account;
use Doctrine\Common\Persistence\ObjectManager;

class PerfectMoneyNumberToAccountTransformer implements DataTransformerInterface
{
    /** @var ObjectManager */
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
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

        $account = $this->objectManager->getRepository('AppBundle:Account')
            ->findOneBy(array('number' => $value));

        if ($account instanceof Account) {

            return $account;
        }

        return null;
    }
}