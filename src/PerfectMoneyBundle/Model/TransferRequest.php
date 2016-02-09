<?php

namespace PerfectMoneyBundle\Model;

class TransferRequest
{
    /** @var string */
    protected $payerAccount;

    /** @var string */
    protected $payeeAccount;

    /** @var string */
    protected $paymentId;

    /** @var string */
    protected $code;

    /** @var int */
    protected $period;

    /** @var  float */
    protected $amount;

    /** @var string */
    private $memo;

    /**
     * @param string $payerAccount
     * @return TransferResponse
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

    /**
     * @return string
     */
    public function getPayeeAccount()
    {
        return $this->payeeAccount;
    }

    /**
     * @param string $payeeAccount
     * @return TransferResponse
     */
    public function setPayeeAccount($payeeAccount)
    {
        $this->payeeAccount = $payeeAccount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     * @return TransferResponse
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * @param string $code
     * @return TransferResponse
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param int $period
     * @return TransferResponse
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @param float $amount
     * @return TransferRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $memo
     * @return TransferRequest
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }
}