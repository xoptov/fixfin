<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Qualification;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\Invoice;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Event\MoneyTransactionEvent;
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

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, Committee $committee)
    {
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->committee = $committee;
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
     * @param Invoice $invoice
     * @return bool
     */
    public function checkInvoiceExpiration(Invoice $invoice)
    {
        $now = new \DateTime();

        if ($now->getTimestamp() > $invoice->getExpiredAt()->getTimestamp()) {
            $invoice->setStatus(Invoice::STATUS_EXPIRED);

            $event = new InvoiceEvent($invoice);
            $this->dispatcher->dispatch(InvoiceEvent::EXPIRED, $event);

            return false;
        }

        return true;
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

            $event = new TicketEvent($ticket);
            $this->dispatcher->dispatch(TicketEvent::EXPIRED, $event);

            return false;
        }

        return true;
    }

    /**
     * @param Account $source
     * @param Account $destination
     * @param float $amount
     * @return MoneyTransaction
     */
    private function createTransaction(Account $source, Account $destination, $amount)
    {
        $transaction = new MoneyTransaction();

        $transaction->setSource($source)
            ->setDestination($destination)
            ->setAmount($amount);

        $this->entityManager->persist($transaction);

        return $transaction;
    }

    /**
     * @param Invoice $invoice
     * @param Account $destination
     * @return MoneyTransaction
     */
    public function createProlongTransaction(Invoice $invoice, Account $destination)
    {
        $source = $invoice->getTicket()->getUser()->getAccount();
        $transaction = $this->createTransaction($source, $destination, $invoice->getAmount());
        $transaction->setInvoice($invoice);

        $event = new MoneyTransactionEvent($transaction);
        $this->dispatcher->dispatch(MoneyTransactionEvent::CREATED, $event);

        return $transaction;
    }

    /**
     * @param Account $source
     * @param Ticket $ticket
     * @return MoneyTransaction
     */
    public function createRewardTransaction(Account $source, Ticket $ticket)
    {
        $destination = $ticket->getUser()->getAccount();
        $rate = $ticket->getRate();
        $amount = $rate->getAmount() * $rate->getCommission() / 100;

        $transaction = $this->createTransaction($source, $destination, $amount);
        $transaction->setType(MoneyTransaction::TYPE_REWARD);

        return $transaction;
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
            ->getReferrersBranch($ticket);

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
}