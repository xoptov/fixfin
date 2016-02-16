<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use AppBundle\Entity\Rate;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRateData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pool = $this->getReference('pool.1');

        $rate = new Rate();
        $rate->setName('Table 0.2 USD')
            ->setAmount(0.2)
            ->setCommission(50)
            ->setPeriod(1)
            ->setRequireQualification(true)
            ->setRequireInvitation(2)
            ->setPool($pool);
        $this->setReference('rate.1', $rate);
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setName('Table 0.5 USD')
            ->setAmount(0.4)
            ->setCommission(30)
            ->setPeriod(2)
            ->setRequireQualification(true)
            ->setRequireInvitation(2)
            ->setPool($pool);
        $this->setReference('rate.2', $rate);
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setName('Table 0.8 USD')
            ->setAmount(0.8)
            ->setCommission(40)
            ->setPeriod(3)
            ->setRequireQualification(true)
            ->setRequireInvitation(1)
            ->setPool($pool);
        $this->setReference('rate.3', $rate);
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setName('Table 1.6 USD')
            ->setAmount(1.6)
            ->setCommission(45)
            ->setPeriod(10)
            ->setRequireQualification(false)
            ->setPool($pool);
        $this->setReference('rate.4', $rate);
        $manager->persist($rate);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadPoolData::class
        );
    }
}