<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Invoice
 */
class Invoice
{
    /** @var int */
    private $id;

    /** @var Ticket */
    private $ticket;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    /** @var \DateTime */
    private $expiredAt;

    /** @var float */
    private $amount;

    /** @var float */
    private $paid;

    /** @var int */
    private $period;

    /** @var int */
    private $status;

    /** @var MoneyTransaction[] */
    private $transactions;

    const STATUS_NEW = 0;
    const STATUS_PARTIAL_PAID = 1;
    const STATUS_PAID = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_CANCELED = 4;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->paid = 0.0;
        $this->status = self::STATUS_NEW;
    }

    public function onCreate()
    {
        $this->createdAt = new \DateTime();
    }

    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_NEW => 'status.invoice.new',
            self::STATUS_PARTIAL_PAID => 'status.invoice.partial_paid',
            self::STATUS_PAID => 'status.invoice.paid',
            self::STATUS_EXPIRED => 'status.invoice.expired',
            self::STATUS_CANCELED => 'status.invoice.canceled'
        ];
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Invoice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param float $paid
     * @return Invoice
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @return float
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param int $period
     * @return Invoice
     */
    public function setPeriod($period)
    {
        $this->period = $period;

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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Invoice
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return Invoice
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $expiredAt
     * @return Invoice
     */
    public function setExpiredAt(\DateTime $expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * @return MoneyTransaction[]|ArrayCollection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param MoneyTransaction[]|ArrayCollection $transactions
     * @return Invoice
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;

        return $this;
    }
}