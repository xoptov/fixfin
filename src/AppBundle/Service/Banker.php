<?php

namespace AppBundle\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Ticket;
use AppBundle\Model\RewardResult;
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

        $amount = round($amount, 2, PHP_ROUND_HALF_DOWN); // Округляем вещественное число до сотых.

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
        $pool = $this->accessor->getValue($ticket, 'rate.pool');
        
        $payerAccount = $this->entityManager->getRepository('AppBundle:Account')
            ->getWealthSystemAccount($pool);

        $payeeAccount = $this->accessor->getValue($ticket, 'user.account');
        $amount = $this->accessor->getValue($ticket, 'rate.amount');
        $commission = $this->accessor->getValue($ticket, 'rate.commission');

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
     */
    public function createPaymentRequest(Invoice $invoice)
    {
        //TODO: Здесь можно реализовать создание запроса в зависимости от платёжной системы
        return $this->perfectMoney->createPaymentRequest($invoice);
    }

    /**
     * @param bool $flush
     * @return null|RewardResult
     */
    public function makeRewardPayments($flush = true)
    {
        // Получаем запланированные транзакции для выполнения
        $transactions = $this->entityManager->getRepository('AppBundle:MoneyTransaction')->getScheduledRewards();

        if (!count($transactions)) {
            return null;
        }

        $result = $this->makeTransfers($transactions);

        // TODO: вот сюда надо втулить диспечеризацию события о том что трансфер обработан.

        if ($flush) {
            $this->entityManager->flush();
        }

        return $result;
    }

    /**
     * @param MoneyTransaction $transaction
     * @return null|RewardResult
     */
    public function processRewardTransaction(MoneyTransaction $transaction)
    {
        if ($transaction->getType() != MoneyTransaction::TYPE_REWARD) {
            return null;
        }

        return $this->makeTransfers($transaction);
    }

    /**
     * @param mixed $transactions
     * @return RewardResult
     */
    public function makeTransfers($transactions)
    {
        if (!is_array($transactions)) {
            $transactions = [$transactions];
        }

        $this->perfectMoney->makeTransfers($transactions);

        $success = $this->perfectMoney->getSuccessful();
        $fail = $this->perfectMoney->getFailed();

        $result = new RewardResult();

        /** @var MoneyTransaction $transaction */
        foreach ($transactions as $transaction) {
            if (isset($success[$transaction->getId()])) {
                // Ставим статус выполнена и записываем номер внешней транзакции
                $transaction->setStatus(MoneyTransaction::STATUS_DONE)
                    ->setExternal($success[$transaction->getId()]);
                $result->addComplete($transaction);
            } elseif (isset($fail[$transaction->getId()])) {
                if ($transaction->getAttempts() >= 3) {
                    // Выставляем статус "ошибка" в транзакцию
                    $transaction->setStatus(MoneyTransaction::STATUS_ERROR);
                    $result->addError($transaction);
                } else {
                    // Выставляем статус "повтор" в транзакцию
                    $transaction->setStatus(MoneyTransaction::STATUS_RETRY);
                    $result->addRepeat($transaction);
                }
                // Записываем тест ошибки и увеличиваем счетчик попоток на единицу
                $transaction->setNote($fail[$transaction->getId()])
                    ->increaseAttempts();
            }
        }

        return $result;
    }
}