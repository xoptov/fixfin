<?php

namespace AppBundle\Service;

use AppBundle\Entity\Qualification;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\Invoice;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Event\TicketEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;

class Cashier
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Committee */
    private $committee;

    /** @var Banker */
    private $banker;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, Committee $committee, Banker $banker)
    {
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->committee = $committee;
        $this->banker = $banker;
    }

    /**
     * @param User $user
     * @param Rate $rate
     * @return Ticket
     */
    public function createTicket(User $user, Rate $rate)
    {
        $ticket = new Ticket();
        $ticket->setRate($rate)
            ->setUser($user);

        $this->entityManager->persist($ticket);

        $event = new TicketEvent($ticket);
        $this->dispatcher->dispatch(TicketEvent::CREATED, $event);

        return $ticket;
    }

    /**
     * @param Ticket $ticket
     * @return Invoice
     */
    public function createInvoice(Ticket $ticket)
    {
        $invoice = new Invoice();
        $invoice->setTicket($ticket)
            ->setAmount($ticket->getRate()->getAmount())
            ->setPeriod($ticket->getRate()->getPeriod());

        $this->entityManager->persist($invoice);

        $event = new InvoiceEvent($invoice);
        $this->dispatcher->dispatch(InvoiceEvent::CREATED, $event);

        return $invoice;
    }

    /**
     * @param Ticket $ticket
     * @return bool
     */
    public function checkTicketExpiration(Ticket $ticket)
    {
        $now = new \DateTime();

        if ($now->getTimestamp() > $ticket->getPaidUp()->getTimestamp()) {
            $ticket->setExpired(true);

            return false;
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    public function checkInvoiceExpiration(Invoice $invoice)
    {
        $now = new \DateTime();

        if ($now->getTimestamp() > $invoice->getExpiredAt()->getTimestamp()) {
            $invoice->setStatus(Invoice::STATUS_EXPIRED);

            return false;
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    public function checkInvoiceFullyPaid(Invoice $invoice)
    {
        if ($invoice->getAmount() > $invoice->getPaid()) {
            $invoice->setStatus(Invoice::STATUS_PARTIAL_PAID);

            return false;
        }

        return true;
    }

    /**
     * @param Ticket $ticket
     */
    private function determineChiefTicket(Ticket $ticket)
    {
        $referrer = $ticket->getUser()->getReferrer();
        $rate = $ticket->getRate();

        if (!$referrer instanceof User) {
            return;
        }

        $refTicket = $this->entityManager->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $referrer);

        if ($refTicket instanceof Ticket) {
            if (!$rate->isRequireQualification()) {
                $ticket->setChiefTicket($refTicket);

                return;
            }
            $qualification = $refTicket->getQualification();

            if ($qualification instanceof Qualification) {
                if ($this->committee->tryPass($qualification)) {
                    $ticket->setChiefTicket($refTicket);

                    return;
                }
            } else {
                $qualification = $this->committee->create($refTicket);
            }

            $qualification->addTransferredTicket($ticket);
        }

        // Если попытка назначения chief ticket из реферрера провалилась тогда пробуем назначить подходящий такет восходя по ветке рефераллов
        $this->determineClosestChiefTicket($ticket);
    }

    /**
     * @param Ticket $ticket
     */
    private function determineClosestChiefTicket(Ticket $ticket)
    {
        $referrers = $this->entityManager->getRepository('AppBundle:User')
            ->getReferrers($ticket);

        if (count($referrers)) {

            $closestTicket = $this->entityManager->getRepository('AppBundle:Ticket')
                ->getClosestTicketByRate($ticket->getRate(), $referrers);

            $ticket->setChiefTicket($closestTicket);
        }
    }

    /**
     * @param User $user
     * @param Rate $rate
     * @param bool $flush
     * @return Ticket
     */
    public function openTable(User $user, Rate $rate, $flush = true)
    {
        $ticket = $this->createTicket($user, $rate);

        if ($rate->isRequireQualification()) {
            $this->committee->create($ticket);
        }

        $this->determineChiefTicket($ticket);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $ticket;
    }

    /**
     * @param Ticket $ticket
     * @return Invoice|mixed
     */
    public function getInvoiceForProlongation(Ticket $ticket)
    {
        $invoice = $this->entityManager->getRepository('AppBundle:Invoice')
            ->getActualInvoice($ticket);

        if ($invoice instanceof Invoice && $this->checkInvoiceExpiration($invoice)) {
            return $invoice;
        }

        return $this->createInvoice($ticket);
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    public function processInvoicePaid(Invoice $invoice, $flush = true)
    {
        if ($this->checkInvoiceFullyPaid($invoice)) {

            $this->reestablishChief($invoice->getTicket());
            $this->reestablishSubordinates($invoice->getTicket());

            if ($flush) {
                $this->entityManager->flush();
            }

            return true;
        }

        return false;
    }

    /**
     * @param Ticket $ticket
     */
    public function reestablishChief(Ticket $ticket)
    {
        $chiefTicket = $ticket->getChiefTicket();

        if (!$chiefTicket) {
            return;
        }

        if ($chiefTicket->isOwnership() || ($chiefTicket->isSubscription() && $this->checkTicketExpiration($chiefTicket))) {
            $this->banker->createRewardTransaction($chiefTicket);

            return;
        }

        // Получаем цепочку реферреров у которых есть оплаченные тикеты для данного стола
        $referrers = $this->entityManager->getRepository('AppBundle:User')
                    ->getReferrers($ticket);

        if (count($referrers)) {

            // Получаем ближний тикет для данного стола из указанной цепочки реферреров
            $closestTicket = $this->entityManager->getRepository('AppBundle:Ticket')
                ->getClosestTicketByRate($ticket->getRate(), $referrers);

            if ($closestTicket) {
                $ticket->setChiefTicket($closestTicket);

                // Кидаем событие о том что chief тикет назначен из цепочки
                $this->dispatcher->dispatch(TicketEvent::CHIEF_REESTABLISHED, new TicketEvent($ticket));
                $this->banker->createRewardTransaction($closestTicket);

                // Выходим до анулирования только что назначенного chief тикета
                return;
            }
        }

        // Если нет подходящего тикета то анулируем chief тикет у текущего тикета
        $ticket->setChiefTicket(null);
        $this->dispatcher->dispatch(TicketEvent::CHIEF_REMOVED, new TicketEvent($ticket));
    }

    /**
     * @param Ticket $ticket
     */
    public function reestablishSubordinates(Ticket $ticket)
    {
        $repo = $this->entityManager->getRepository('AppBundle:Ticket');

        if ($ticket->getQualification()) {
            $lostTickets = $repo->getLostReferralsTickets($ticket);
        } else {
            $lostTickets = $repo->getLostReferralsTickets($ticket, false);
        }

        if (!count($lostTickets)) {
            return;
        }

        foreach ($lostTickets as $subordinate) {
            $subordinate->setChiefTicket($ticket);
        }

        $this->dispatcher->dispatch(TicketEvent::SUBORDINATES_REESTABLISHED, new TicketEvent($ticket));
    }
}