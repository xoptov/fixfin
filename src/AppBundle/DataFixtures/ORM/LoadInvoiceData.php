<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\Ticket;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInvoiceData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $invoice = new Invoice();
        $invoice
            ->setAmount(0.2)
            ->setTicket($this->getReference('ticket.2'))
            ->setExpiredAt(new \DateTime('+1 week'))
            ->setPaid(0.2)->setPeriod(7)
            ->setStatus(Invoice::STATUS_PAID);
        $this->setReference('invoice.1', $invoice);
        $manager->persist($invoice);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadTicketData::class
        );
    }
}