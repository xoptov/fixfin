<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\MoneyTransaction;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMoneyTransactionData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transaction = new MoneyTransaction();
        $transaction->setSource($this->getReference('account.3'))
            ->setDestination($this->getReference('system.account'))
            ->setStatus(MoneyTransaction::STATUS_DONE)->setAmount(0.2)
            ->setInvoice($this->getReference('invoice.1'))
            ->setType(MoneyTransaction::TYPE_PROLONG)->setExternal(39724862);
        $this->setReference('transaction.1', $transaction);
        $manager->persist($transaction);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadAccountData::class,
            LoadInvoiceData::class
        );
    }
}