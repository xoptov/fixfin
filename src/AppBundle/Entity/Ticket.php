<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ticket
 */
class Ticket
{
    /** @var int */
    private $id;

    /** @var \DateTime */
    private $paidUp;

    /** @var Ticket */
    private $chiefTicket;

    /** @var Ticket[] */
    private $subordinates;

    /** @var Invoice[] */
    private $invoices;

    /** @var User */
    private $user;

    /** @var Rate */
    private $rate;

    /** @var int */
    private $type;

    /** @var Qualification */
    private $qualification;

    /** @var bool */
    private $expired;

    const TYPE_SUBSCRIPTION = 1;
    const TYPE_OWNERSHIP = 2;

    public function __construct()
    {
        $this->subordinates = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->type = self::TYPE_SUBSCRIPTION;
        $this->expired = true;
    }

    public static function getTypeLabels()
    {
        return [
            static::TYPE_SUBSCRIPTION => 'type.ticket.subscription',
            static::TYPE_OWNERSHIP => 'type.ticket.ownership'
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $paidUp
     * @return Ticket
     */
    public function setPaidUp($paidUp)
    {
        $this->paidUp = $paidUp;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaidUp()
    {
        return $this->paidUp;
    }

    /**
     * @param Ticket $ticket
     * @return Ticket
     */
    public function setChiefTicket(Ticket $ticket)
    {
        $this->chiefTicket = $ticket;

        return $this;
    }

    /**
     * @return Ticket
     */
    public function getChiefTicket()
    {
        return $this->chiefTicket;
    }

    /**
     * @param Ticket[]|ArrayCollection $subordinates
     * @return Ticket
     */
    public function setSubordinates($subordinates)
    {
        $this->subordinates = $subordinates;

        return $this;
    }

    /**
     * @return Ticket[]|ArrayCollection
     */
    public function getSubordinates()
    {
        return $this->subordinates;
    }

    /**
     * @param User $user
     * @return Ticket
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Rate $rate
     * @return Ticket
     */
    public function setRate(Rate $rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return Rate
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param int $type
     * @return Ticket
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
     * @param Qualification $qualification
     * @return Ticket
     */
    public function setQualification(Qualification $qualification)
    {
        $this->qualification = $qualification;

        return $this;
    }

    /**
     * @return Qualification
     */
    public function getQualification()
    {
        return $this->qualification;
    }

    /**
     * @param bool $value
     * @return Ticket
     */
    public function setExpired($value)
    {
        $this->expired = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->expired;
    }

    /**
     * @return bool
     */
    public function isSubscription()
    {
        return self::TYPE_SUBSCRIPTION === $this->getType();
    }

    /**
     * @return bool
     */
    public function isOwnership()
    {
        return self::TYPE_OWNERSHIP === $this->getType();
    }
}