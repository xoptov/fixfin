<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Pool;

class LoadPoolData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pool = new Pool();
        $pool->setName('main_pool')
            ->addAccount($this->getReference('system.account'));
        $manager->persist($pool);
        $this->setReference('pool.1', $pool);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadAccountData::class
        );
    }
}