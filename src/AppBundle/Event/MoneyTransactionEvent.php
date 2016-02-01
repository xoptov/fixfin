<?php

namespace AppBundle\Event;

use AppBundle\Entity\MoneyTransaction;
use Symfony\Component\EventDispatcher\Event;

class MoneyTransactionEvent extends Event
{
    const CREATED = 'money_transaction.created';

    /** @var MoneyTransaction */
    private $moneyTransaction;

    /**
     * @param MoneyTransaction $moneyTransaction
     */
    public function __construct(MoneyTransaction $moneyTransaction)
    {
        $this->moneyTransaction = $moneyTransaction;
    }

    /**
     * @return MoneyTransaction
     */
    public function getMoneyTransaction()
    {
        return $this->moneyTransaction;
    }
}