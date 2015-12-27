<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Account
 */
class Account
{
    /** @var int */
    private $id;

    /** @var User */
    private $user;

    /** @var string */
    private $number;

    /** @var string */
    private $password;

    /** @var string */
    private $passPhrase;

    /** @var float */
    private $commission;

    /** @var float */
    private $balance;

    /** @var int */
    private $type;

    const TYPE_SYSTEM = 1;
    const TYPE_CUSTOMER = 2;

    /** @var Pool */
    private $pools;

    /** @var ArrayCollection */
    private $incomes;

    /** @var ArrayCollection */
    private $outcomes;

    /** @var bool */
    private $verified;

    /** @var bool */
    private $blocked;

    public function __construct()
    {
        $this->pools = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->outcomes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param User $user
     * @return Account
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
     * @param string $number
     * @return Account
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $password
     * @return Account
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $passPhrase
     * @return Account
     */
    public function setPassPhrase($passPhrase)
    {
        $this->passPhrase = $passPhrase;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    /**
     * @param float $commission
     * @return Account
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
     * @param float $balance
     * @return Account
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param int $type
     * @return Account
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
     * @param bool $verified
     * @return Account
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @param bool $blocked
     * @return Account
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->blocked;
    }
}