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
     * @param Account $payeeAccount
     * @param Account $payerAccount
     * @param float $amount
     * @return MoneyTransaction
     */
    private function createTransaction(Account $payerAccount, Account $payeeAccount, $amount)
    {
        $transaction = new MoneyTransaction();

        $transaction->setSource($payerAccount)
            ->setDestination($payeeAccount)
            ->setAmount($amount);

        $this->entityManager->persist($transaction);

        return $transaction;
    }

    /**
     * Этот метод использовать для того чтобы создать логирующую транзакцию после получения подтверждения об оплате от Payment System
     * @param Invoice $invoice
     * @param Account $payeeAccount
     * @return MoneyTransaction
     */
    public function createProlongTransaction(Invoice $invoice, Account $payeeAccount)
    {
        $payerAccount = $this->accessor->getValue($invoice, 'ticket.user.account');

        $transaction = $this->createTransaction($payerAccount, $payeeAccount, $invoice->getPaid())
            ->setType(MoneyTransaction::TYPE_PROLONG)
            ->setInvoice($invoice);

        return $transaction;
    }

    /**
     * @param Ticket $ticket
     * @return MoneyTransaction
     */
    public function createRewardTransaction(Ticket $ticket)
    {
        $payeeAccount = $this->accessor->getValue($ticket, 'user.account');
        $pool = $this->accessor->getValue($ticket, 'rate.pool');
        $amount = $this->accessor->getValue($ticket, 'rate.amount');
        $commission = $this->accessor->getValue($ticket, 'rate.commission');

        $payerAccount = $this->entityManager->getRepository('AppBundle:Account')
            ->getWealthSystemAccount($pool);

        $result = $amount - $amount * $commission / 100;

        $transaction = $this->createTransaction($payerAccount, $payeeAccount, $result)
            ->setType(MoneyTransaction::TYPE_REWARD);

        return $transaction;
    }

    /**
     * @param Account $payerAccount
     * @param Account $payeeAccount
     * @param float $amount
     * @return MoneyTransaction
     */
    public function createWithdrawalTransaction(Account $payerAccount, Account $payeeAccount, $amount)
    {
        $transaction = $this->createTransaction($payerAccount, $payeeAccount, $amount)
            ->setType(MoneyTransaction::TYPE_WITHDRAWAL);

        return $transaction;
    }

    /**
     * @param Invoice $invoice
     * @return PaymentRequest
     * @todo Подумать куда лучше вынести этот метод, а может и оставить его
     */
    public function createPaymentRequest(Invoice $invoice)
    {
        //TODO: Здесь можно реализовать создание запроса в зависимости от платёжной системы
        return $this->perfectMoney->createPaymentRequest($invoice);
    }
}