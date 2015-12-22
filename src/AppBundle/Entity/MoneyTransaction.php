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

    /** @var int */
    private $type;

    /** @var string */
    private $external;

    /** @var Ticket */
    private $ticket;

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
     * @param Ticket $ticket
     * @return MoneyTransaction
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}

