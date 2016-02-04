<?php

namespace PerfectMoneyBundle\Model;

interface PaymentResultInterface
{
    /**
     * @param string $payeeAccount
     * @return PaymentResultInterface
     */
    public function setPayeeAccount($payeeAccount);

    /**
     * @return string
     */
    public function getPayeeAccount();

    /**
     * @param float $paymentAmount
     * @return PaymentResultInterface
     */
    public function setPaymentAmount($paymentAmount);

    /**
     * @return float
     */
    public function getPaymentAmount();

    /**
     * @param string $paymentUnits
     * @return PaymentResultInterface
     */
    public function setPaymentUnits($paymentUnits);

    /**
     * @return string
     */
    public function getPaymentUnits();

    /**
     * @param int $paymentBatchNum
     * @return PaymentResultInterface
     */
    public function setPaymentBatchNum($paymentBatchNum);

    /**
     * @return int
     */
    public function getPaymentBatchNum();

    /**
     * @param string $paymentId
     * @return PaymentResultInterface
     */
    public function setPaymentId($paymentId);

    /**
     * @return string
     */
    public function getPaymentId();
}