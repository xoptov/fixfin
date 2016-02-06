<?php

namespace PerfectMoneyBundle\Model;

interface PaymentInterface
{
    /**
     * @param string $payeeAccount
     * @return PaymentInterface
     */
    public function setPayeeAccount($payeeAccount);

    /**
     * @return string
     */
    public function getPayeeAccount();

    /**
     * @param float $paymentAmount
     * @return PaymentInterface
     */
    public function setPaymentAmount($paymentAmount);

    /**
     * @return float
     */
    public function getPaymentAmount();

    /**
     * @param string $paymentUnits
     * @return PaymentInterface
     */
    public function setPaymentUnits($paymentUnits);

    /**
     * @return string
     */
    public function getPaymentUnits();

    /**
     * @param int $paymentBatchNum
     * @return PaymentInterface
     */
    public function setPaymentBatchNum($paymentBatchNum);

    /**
     * @return int
     */
    public function getPaymentBatchNum();

    /**
     * @param string $paymentId
     * @return PaymentInterface
     */
    public function setPaymentId($paymentId);

    /**
     * @return string
     */
    public function getPaymentId();
}