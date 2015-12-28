<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Rate
 */
class Rate
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var float */
    private $amount;

    /** @var float */
    private $commission;

    /** @var int */
    private $period;

    /** @var int */
    private $members;

    /** @var Pool */
    private $pool;

    /** @var ArrayCollection */
    private $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->members = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return Rate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param float $amount
     * @return Rate
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
     * @param float $commission
     * @return Rate
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param int $period
     * @return Rate
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
     * @param integer $members
     * @return Rate
     */
    public function setMembers($members)
    {
        $this->members = $members;

        return $this;
    }

    /**
     * @return int
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param Pool $pool
     * @return Rate
     */
    public function setPool(Pool $pool)
    {
        $this->pool = $pool;

        return $this;
    }

    /**
     * @return Pool
     */
    public function getPool()
    {
        return $this->pool;
    }
}

