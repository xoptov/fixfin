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
    private $merchantPassPhrase;

    /** @var float */
    private $commission;

    /** @var float */
    private $balance;

    /** @var bool */
    private $system;

    const STATUS_NOT_SYSTEM = 0;
    const STATUS_SYSTEM = 1;

    /** @var Pool */
    private $pools;

    /** @var ArrayCollection */
    private $incomes;

    /** @var ArrayCollection */
    private $outcomes;

    /** @var bool */
    private $verified;

    const STATUS_NOT_VERIFIED = 0;
    const STATUS_VERIFIED = 1;

    /** @var bool */
    private $blocked;

    const STATUS_OPENED = 0;
    const STATUS_BLOCKED = 1;

    public function __construct()
    {
        $this->pools = new ArrayCollection();
        $this->incomes = new ArrayCollection();
        $this->outcomes = new ArrayCollection();
        $this->system = false;
    }

    public static function getSystemLabels()
    {
        return [
            Account::STATUS_NOT_SYSTEM => 'entity.account.status.no',
            Account::STATUS_SYSTEM => 'entity.account.status.yes'
        ];
    }

    public static function getVerificationLabels()
    {
        return [
            Account::STATUS_NOT_VERIFIED => 'entity.account.status.no',
            Account::STATUS_VERIFIED => 'entity.account.status.yes'
        ];
    }

    public static function getBlockLabels()
    {
        return [
            Account::STATUS_OPENED => 'entity.account.status.no',
            Account::STATUS_BLOCKED => 'entity.account.status.yes'
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
     * @param string $merchantPassPhrase
     * @return Account
     */
    public function setMerchantPassPhrase($merchantPassPhrase)
    {
        $this->merchantPassPhrase = $merchantPassPhrase;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantPassPhrase()
    {
        return $this->merchantPassPhrase;
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