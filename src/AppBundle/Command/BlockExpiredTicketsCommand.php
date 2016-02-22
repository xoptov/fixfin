<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Ticket;

class BlockExpiredTicketsCommand extends ContainerAwareCommand
{
    const FIRST = 0;

    protected function configure()
    {
        $this->setName('ticket:block:expired');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $cashier = $container->get('app.cashier_service');

        $tickets = $entityManager
            ->getRepository('AppBundle:Ticket')
            ->getWithExpiredDate();

        if (!count($tickets)) {
            $output->writeln('No expired tickets.');

            return;
        }

            /** @var Ticket $ticket */
        foreach ($tickets as $ticket) {
            $ticket->setExpired(true);
            $subordinates = $ticket->getSubordinates();

            $closestChiefTicket = null;

            for ($i = self::FIRST; $i < count($subordinates); $i++) {
                if ($i == self::FIRST) {
                    $closestChiefTicket = $cashier->determineClosestChiefTicket($subordinates[$i]);
                }
                $subordinates[$i]->setChiefTicket($closestChiefTicket);
            }
        }

        $entityManager->flush();

        $output->writeln('Expired ' . count($tickets) . ' tickets.');
    }
}