<?php

namespace AppBundle\Service;

use AppBundle\Entity\Qualification;
use AppBundle\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Committee
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PropertyAccessor */
    private $accessor;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, PropertyAccessor $accessor)
    {
        $this->entityManager = $entityManager;
        $this->accessor = $accessor;
    }

    /**
     * @param Ticket $ticket
     * @return Qualification
     */
    public function create(Ticket $ticket)
    {
        $qualification = new Qualification();
        $requireInvitation = $this->accessor->getValue($ticket, 'rate.requireInvitation');
        $qualification->setRequireInvitation($requireInvitation)
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
        $transferred = $this->accessor->getValue($qualification, 'transferredTickets');

        if ($qualification->getRequireInvitation() <= count($transferred)) {
            $qualification->setPassed(true);

            return true;
        }

        return false;
    }
}