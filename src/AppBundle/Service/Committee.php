<?php

namespace AppBundle\Service;

use AppBundle\Entity\Qualification;
use AppBundle\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;

class Committee
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Ticket $ticket
     * @return Qualification
     */
    public function create(Ticket $ticket)
    {
        $qualification = new Qualification();
        $qualification->setRequireInvitation($ticket->getRate()->getRequireInvitation())
            ->setTicket($ticket);

        $this->entityManager->persist($qualification);

        return $qualification;
    }

    /**
     * @param Qualification $qualification
     * @return bool
     */
    public function tryPass(Qualification $qualification)
    {
        $transferred = $qualification->getTransferredTickets()->count();
        if ($qualification->getRequireInvitation() <= $transferred) {
            $qualification->setPassed(true);

            return true;
        }

        return false;
    }
}