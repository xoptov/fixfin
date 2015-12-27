<?php

namespace AppBundle\Entity;

/**
 * MoneyTransaction
 */
class MoneyTransaction
{
    /** @var int */
    private $id;

    /** @var Account */
    private $source;

    /** @var Account */
    private $destination;

    /** @var float */
    private $amount;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    /** @var int */
    private $status;

    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    /** @var string */
    private $note;

    /** @var int */
    private $type;

    const TYPE_RENEWAL = 1;

    /** @var string */
    private $external;

    /** @var Invoice */
    private $invoice;

    public function __construct()
    {
        $this->amount = 0;
        $this->status = self::STATUS_NEW;
        $this->type = self::TYPE_RENEWAL;
    }

    public function onCreate()
    {
        $this->createdAt = new \DateTime();
    }

    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Account $source
     * @return MoneyTransaction
     */
    public function setSource(Account $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Account
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param Account $destination
     * @return MoneyTransaction
     */
    public function setDestination(Account $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return Account
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param float $amount
     * @return MoneyTransaction
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param integer $status
     * @return MoneyTransaction
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $note
     * @return MoneyTransaction
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param integer $type
     * @return MoneyTransaction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $external
     * @return MoneyTransaction
     */
    public function setExternal($external)
    {
        $this->external = $external;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternal()
    {
        return $this->external;
    }

    /**
     * @param Invoice $invoice
     * @return MoneyTransaction
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}

