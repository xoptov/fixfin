<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;

class Banker
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
     * @return MoneyTransaction
     */
    public function createProlongTransaction(Invoice $invoice)
    {
        $source = $invoice->getTicket()->getUser()->getAccount();
        $destination = $this->entityManager->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($invoice->getTicket()->getRate()->getPool());

        $transaction = $this->createTransaction($source, $destination, $invoice->getAmount());
        $transaction->setInvoice($invoice);

        return $transaction;
    }

    /**
     * @param Ticket $ticket
     * @return MoneyTransaction
     */
    public function createRewardTransaction(Ticket $ticket)
    {
        $destination = $ticket->getUser()->getAccount();
        $source = $this->entityManager->getRepository('AppBundle:Account')
            ->getWealthSystemAccount($ticket->getRate()->getPool());

        $rate = $ticket->getRate();
        $amount = $rate->getAmount() * $rate->getCommission() / 100;

        $transaction = $this->createTransaction($source, $destination, $amount);

        return $transaction;
    }
}