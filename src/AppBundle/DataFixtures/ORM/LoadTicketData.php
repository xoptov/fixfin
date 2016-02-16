<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Ticket;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTicketData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $ticket = new Ticket();
        $ticket
            ->setUser($this->getReference('user.2'))
            ->setRate($this->getReference('rate.1'));
        $this->setReference('ticket.1', $ticket);
        $manager->persist($ticket);

        $ticket = new Ticket();
        $ticket
            ->setUser($this->getReference('user.3'))
            ->setRate($this->getReference('rate.1'))
            ->setPaidUp(new \DateTime('+1 week'))
            ->setExpired(false);
        $this->setReference('ticket.2', $ticket);
        $manager->persist($ticket);

        $ticket = new Ticket();
        $ticket
            ->setUser($this->getReference('user.10'))
            ->setRate($this->getReference('rate.1'))
            ->setPaidUp(new \DateTime('-3 days'))
            ->setExpired(true);
        $this->setReference('ticket.3', $ticket);
        $manager->persist($ticket);

        $ticket = new Ticket();
        $ticket
            ->setUser($this->getReference('user.11'))
            ->setRate($this->getReference('rate.1'))
            ->setPaidUp(new \DateTime('+5 days'))
            ->setExpired(false);
        $this->setReference('ticket.4', $ticket);
        $manager->persist($ticket);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadUserData::class,
            LoadRateData::class
        );
    }
}