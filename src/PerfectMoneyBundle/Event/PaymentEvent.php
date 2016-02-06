<?php

namespace PerfectMoneyBundle\Event;

use PerfectMoneyBundle\Model\PaymentInterface;

class PaymentEvent
{
    const FAILED = 'perfect_money.payment.failed';
    const SUCCESS = 'perfect_money.payment.success';
    const CONFIRMED = 'perfect_money.payment.confirmed';

    /** @var PaymentInterface */
    private $result;

    public function __construct(PaymentInterface $result)
    {
        $this->result = $result;
    }

    /**
     * @return PaymentInterface
     */
    public function getResult()
    {
        return $this->result;
    }
}