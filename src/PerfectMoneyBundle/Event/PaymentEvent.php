<?php

namespace PerfectMoneyBundle\Event;

use PerfectMoneyBundle\Model\PaymentResultInterface;

class PaymentEvent
{
    const FAILED = 'perfect_money.payment.failed';
    const SUCCESS = 'perfect_money.payment.success';
    const CONFIRMED = 'perfect_money.payment.confirmed';

    /** @var PaymentResultInterface */
    private $result;

    public function __construct(PaymentResultInterface $result)
    {
        $this->result = $result;
    }

    /**
     * @return PaymentResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }
}