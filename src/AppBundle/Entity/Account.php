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
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $passPhrase;

    /** @var float */
    private $commission;

    /** @var float */
    private $balance;

    /** @var float */
    private $profit;

    /** @var bool */
    private $system;

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
        $this->balance = 0.0;
        $this->profit = 0.0;
        $this->pools = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->outcomes = new ArrayCollection();
        $this->system = false;
        $this->blocked = false;
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
     * @param string $login
     * @return Account
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
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
     * @param float $value
     * @return Account
     */
    public function setBalance($value)
    {
        $this->balance = $value;

        return $this;
    }

    /**
     * @param $value
     * @return Account
     */
    public function increaseBalance($value)
    {
        $this->balance += $value;

        return $this;
    }

    /**
     * @param $value
     * @return Account
     */
    public function reduceBalance($value)
    {
        $this->balance -= $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $value
     * @return Account
     */
    public function setProfit($value)
    {
        $this->profit = $value;

        return $this;
    }

    /**
     * @param float $value
     * @return Account
     */
    public function increaseProfit($value)
    {
        $this->profit += $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * @param bool $value
     * @return Account
     */
    public function setSystem($value)
    {
        $this->system = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSystem()
    {
        return $this->system;
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