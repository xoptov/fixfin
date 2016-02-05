<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Service\PaymentSystem\PerfectMoney;
use PerfectMoneyBundle\Model\PaymentRequest;

class Banker
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PropertyAccessor */
    private $accessor;

    /** @var PerfectMoney */
    private $perfectMoney;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, PropertyAccessor $accessor, PerfectMoney $perfectMoney)
    {
        $this->entityManager = $entityManager;
        $this->accessor = $accessor;
        $this->perfectMoney = $perfectMoney;
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
        $source = $this->accessor->getValue($invoice, 'ticket.user.account');
        $pool = $this->accessor->getValue($invoice, 'ticket.rate.pool');

        $destination = $this->entityManager->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($pool);

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
        $destination = $this->accessor->getValue($ticket, 'user.account');
        $pool = $this->accessor->getValue($ticket, 'rate.pool');

        $source = $this->entityManager->getRepository('AppBundle:Account')
            ->getWealthSystemAccount($pool);

        $rate = $ticket->getRate();
        $amount = $rate->getAmount() * $rate->getCommission() / 100;

        $transaction = $this->createTransaction($source, $destination, $amount);

        return $transaction;
    }

    /**
     * @param Invoice $invoice
     * @return PaymentRequest
     */
    public function createPaymentRequest(Invoice $invoice)
    {
        //TODO: Здесь можно реализовать создание запроса в зависимости от платёжной системы
        return $this->perfectMoney->createPaymentRequest($invoice);
    }
}