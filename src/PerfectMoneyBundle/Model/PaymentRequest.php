<?php

namespace AppBundle\Model;

class PaymentRequest
{
    /** @var string */
    private $payeeAccount;

    /** @var string */
    private $payeeName;

    /** @var float */
    private $paymentAmount;

    /** @var string */
    private $paymentUnits;

    /** @var string */
    private $paymentId;

    /** @var string */
    private $statusUrl;

    /** @var string */
    private $paymentUrl;

    /** @var string */
    private $noPaymentUrl;

    /** @var string */
    private $availablePaymentMethods;

    /**
     * @param string $payeeAccount
     * @return PaymentRequest
     */
    public function setPayeeAccount($payeeAccount)
    {
        $this->payeeAccount = $payeeAccount;

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
     * @param string $payeeName
     * @return PaymentRequest
     */
    public function setPayeeName($payeeName)
    {
        $this->payeeName = $payeeName;

        return $this;
    }

    /**
     * @param float $paymentAmount
     * @return PaymentRequest
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

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
     * @param string $paymentUnits
     * @return PaymentRequest
     */
    public function setPaymentUnits($paymentUnits)
    {
        $this->paymentUnits = $paymentUnits;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentUnits()
    {
        return $this->paymentUnits;
    }

    /**
     * @param string $paymentId
     * @return PaymentRequest
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * @param string $statusUrl
     * @return PaymentRequest
     */
    public function setStatusUrl($statusUrl)
    {
        $this->statusUrl = $statusUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->statusUrl;
    }

    /**
     * @param string $paymentUrl
     * @return PaymentRequest
     */
    public function setPaymentUrl($paymentUrl)
    {
        $this->paymentUrl = $paymentUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->paymentUrl;
    }

    /**
     * @param string $noPaymentUrl
     * @return PaymentRequest
     */
    public function setNoPaymentUrl($noPaymentUrl)
    {
        $this->noPaymentUrl = $noPaymentUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getNoPaymentUrl()
    {
        return $this->noPaymentUrl;
    }

    /**
     * @param string $methods
     * @return PaymentRequest
     */
    public function setAvailablePaymentMethods($methods)
    {
        $this->availablePaymentMethods = $methods;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvailablePaymentMethods()
    {
        return $this->availablePaymentMethods;
    }
}