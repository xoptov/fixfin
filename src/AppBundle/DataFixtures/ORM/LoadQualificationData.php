<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Qualification;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadQualificationData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $qualification = new Qualification();
        $qualification->setRequireInvitation(2)
            ->setPassed(true)
            ->setTicket($this->getReference('ticket.2'))
            ->setTransferredTickets(array(
                $this->getReference('ticket.3'),
                $this->getReference('ticket.4')
            ));
        $manager->persist($qualification);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadTicketData::class
        );
    }
}