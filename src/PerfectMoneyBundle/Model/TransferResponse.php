<?php

namespace PerfectMoneyBundle\Model;

class TransferResponse extends TransferRequest implements ResponseInterface
{
    /** @var string */
    private $error;

    /** @var string */
    private $payeeAccountName;

    /** @var int */
    private $paymentBatchNum;

    /**
     * @param string $error
     * @return TransferResponse
     */
    public function setError($error)
    {
        $this->error = $error;

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
     * @param string $payeeAccountName
     * @return TransferResponse
     */
    public function setPayeeAccountName($payeeAccountName)
    {
        $this->payeeAccountName = $payeeAccountName;

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
     * @param float $amount
     * @return TransferResponse
     */
    public function setPaymentAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $paymentBatchNum
     * @return TransferResponse
     */
    public function setPaymentBatchNum($paymentBatchNum)
    {
        $this->paymentBatchNum = $paymentBatchNum;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentBatchNum()
    {
        return $this->paymentBatchNum;
    }
}