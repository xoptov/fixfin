<?php

namespace PerfectMoneyBundle\Model;

class PaymentSuccess extends PaymentError
{
    /** @var string */
    protected $payerAccount;

    /**
     * @param string $payerAccount
     * @return PaymentSuccess
     */
    public function setPayerAccount($payerAccount)
    {
        $this->payerAccount = $payerAccount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerAccount()
    {
        return $this->payerAccount;
    }
}