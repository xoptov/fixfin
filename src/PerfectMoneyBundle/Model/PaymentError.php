<?php

namespace PerfectMoneyBundle\Model;

class PaymentError implements PaymentResultInterface
{
    /** @var string */
    protected $payeeAccount;

    /** @var float */
    protected $paymentAmount;

    /** @var string */
    protected $paymentUnits;

    /** @var int */
    protected $paymentBatchNum;

    /** @var string */
    protected $paymentId;

    /**
     * {@inherited}
     */
    public function setPayeeAccount($payeeAccount)
    {
        $this->payeeAccount = $payeeAccount;

        return $this;
    }

    /**
     * {@inherited}
     */
    public function getPayeeAccount()
    {
        return $this->payeeAccount;
    }

    /**
     * {@inherited}
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * {@inherited}
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * {@inherited}
     */
    public function setPaymentUnits($paymentUnits)
    {
        $this->paymentUnits = $paymentUnits;

        return $this;
    }

    /**
     * {@inherited}
     */
    public function getPaymentUnits()
    {
        return $this->paymentUnits;
    }

    /**
     * {@inherited}
     */
    public function setPaymentBatchNum($paymentBatchNum)
    {
        $this->paymentBatchNum = $paymentBatchNum;

        return $this;
    }

    /**
     * {@inherited}
     */
    public function getPaymentBatchNum()
    {
        return $this->paymentBatchNum;
    }

    /**
     * {@inherited}
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * {@inherited}
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }
}