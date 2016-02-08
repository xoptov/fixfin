<?php

namespace PerfectMoneyBundle\Model;

class SpentResponse implements ResponseInterface
{
    /** @var string */
    private $error;

    /** @var string */
    private $payeeAccountName;

    /** @var string */
    private $payerAccount;

    /** @var string */
    private $payeeAccount;

    /** @var float */
    private $paymentAmount;

    /** @var int */
    private $paymentBatchNum;

    /** @var string */
    private $paymentId;

    /** @var string */
    private $code;

    /** @var int */
    private $period;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return SpentResponse
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return SpentResponse
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
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
     * @return SpentResponse
     */
    public function setPayeeAccount($payeeAccount)
    {
        $this->payeeAccount = $payeeAccount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayeeAccountName()
    {
        return $this->payeeAccountName;
    }

    /**
     * @param string $payeeAccountName
     * @return SpentResponse
     */
    public function setPayeeAccountName($payeeAccountName)
    {
        $this->payeeAccountName = $payeeAccountName;

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
     * @param string $payerAccount
     * @return SpentResponse
     */
    public function setPayerAccount($payerAccount)
    {
        $this->payerAccount = $payerAccount;

        return $this;
    }

    /**
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * @param float $paymentAmount
     * @return SpentResponse
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentBatchNum()
    {
        return $this->paymentBatchNum;
    }

    /**
     * @param int $paymentBatchNum
     * @return SpentResponse
     */
    public function setPaymentBatchNum($paymentBatchNum)
    {
        $this->paymentBatchNum = $paymentBatchNum;

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
     * @return SpentResponse
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
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
     * @return SpentResponse
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }


}