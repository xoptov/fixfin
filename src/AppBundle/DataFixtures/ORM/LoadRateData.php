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
        $rate->setName('Table 20$')
            ->setAmount(20.0)
            ->setCommission(20)
            ->setPeriod(30)
            ->setPool($pool);
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setName('Table 40$')
            ->setAmount(40.0)
            ->setCommission(30)
            ->setPeriod(30)
            ->setPool($pool);
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setName('Table 80$')
            ->setAmount(80.0)
            ->setCommission(35)
            ->setPeriod(30)
            ->setPool($pool);
        $manager->persist($rate);

        $rate = new Rate();
        $rate->setName('Table 160$')
            ->setAmount(160.0)
            ->setCommission(40)
            ->setPeriod(45)
            ->setPool($pool);
        $manager->persist($rate);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'AppBundle\\DataFixtures\\ORM\\LoadPoolData'
        );
    }
}