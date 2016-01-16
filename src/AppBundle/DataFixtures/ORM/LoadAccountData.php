<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Account;

class LoadAccountData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $account = new Account();
        $account->setBalance(100)
            ->setCommission(0.5)
            ->setNumber('U9102389')
            ->setLogin('129839')
            ->setPassPhrase('theSecretPassPhrase')
            ->setSystem(true)
            ->setVerified(true);
        $manager->persist($account);
        $this->setReference('system.account', $account);

        $account = new Account();
        $account->setNumber('U2482738');
        $manager->persist($account);
        $this->setReference('account.1', $account);

        $account = new Account();
        $account->setNumber('U9387212');
        $manager->persist($account);
        $this->setReference('account.2', $account);

        $account = new Account();
        $account->setNumber('U6482911');
        $manager->persist($account);
        $this->setReference('account.3', $account);

        $account = new Account();
        $account->setNumber('U3487510');
        $manager->persist($account);
        $this->setReference('account.4', $account);

        $account = new Account();
        $account->setNumber('U1204862');
        $manager->persist($account);
        $this->setReference('account.5', $account);

        $account = new Account();
        $account->setNumber('U7364727');
        $manager->persist($account);
        $this->setReference('account.6', $account);

        $account = new Account();
        $account->setNumber('U5731290');
        $manager->persist($account);
        $this->setReference('account.7', $account);

        $manager->flush();
    }
}