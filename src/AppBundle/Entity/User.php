<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;

class User extends BaseUser
{
    /** @var string */
    private $firstName;

    /** @var string */
    private $middleName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $phone;

    /** @var Account */
    private $account;

    /** @var Ticket[] */
    private $tickets;

    /** @var User */
    private $referrer;

    /** @var User[] */
    private $referrals;

    /** @var string */
    private $avatar;

    public function __construct()
    {
        parent::__construct();
        $this->tickets = new ArrayCollection();
        $this->referrals = new ArrayCollection();
    }
    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
       return $this->middleName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param Account $account
     * @return User
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param User $referrer
     * @return User
     */
    public function setReferrer(User $referrer)
    {
        $this->referrer = $referrer;

        return $this;
    }

    /**
     * @return User
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}