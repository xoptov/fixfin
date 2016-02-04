<?php

namespace AppBundle\Model;

use PerfectMoneyBundle\Model\PaymentSuccess;

class PaymentConfirmation extends PaymentSuccess
{
    /** @var int */
    protected $timestampGmt;

    /** @var string */
    protected $v2Hash;

    /**
     * @param int $timestampGmt
     * @return PaymentConfirmation
     */
    public function setTimestampGmt($timestampGmt)
    {
        $this->timestampGmt = $timestampGmt;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestampGmt()
    {
        return $this->timestampGmt;
    }

    /**
     * @param string $v2Hash
     * @return PaymentConfirmation
     */
    public function setV2Hash($v2Hash)
    {
        $this->v2Hash = $v2Hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getV2Hash()
    {
        return $this->v2Hash;
    }
}