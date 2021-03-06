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

    /** @var Ticket[] */
    private $tickets;

    /** @var int */
    private $score;

    /** @var bool */
    private $requireQualification;

    /** @var int */
    private $requireInvitation;

    /** @var int */
    private $requireScore;

    public function __construct()
    {
        $this->members = 0;
        $this->requireQualification = false;
        $this->requireInvitation = 0;
        $this->tickets = new ArrayCollection();
        $this->requireScore = 0;
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

    public function increaseMembers()
    {
        $this->members++;
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
     * @param bool $value
     * @return Rate
     */
    public function setRequireQualification($value)
    {
        $this->requireQualification = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequireQualification()
    {
        return $this->requireQualification;
    }

    /**
     * @param int $amount
     * @return Rate
     */
    public function setRequireInvitation($amount)
    {
        $this->requireInvitation = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequireInvitation()
    {
        return $this->requireInvitation;
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

    /**
     * @param int $score
     * @return Rate
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     * @return Rate
     */
    public function setRequireScore($score)
    {
        $this->requireScore = $score;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequireScore()
    {
        return $this->requireScore;
    }
}

