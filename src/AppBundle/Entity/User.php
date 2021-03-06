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

    /** @var string */
    private $avatar;

    /** @var string */
    private $vkontakte;

    /** @var User */
    private $referrer;

    /** @var User[] */
    private $referrals;

    /** @var integer */
    private $level;

    /** @var string */
    private $path;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    /** @var bool */
    private $canInvite;

    /** @var integer */
    private $score;

    /** @var Notification[] */
    private $notifications;

    public function __construct()
    {
        parent::__construct();
        $this->tickets = new ArrayCollection();
        $this->referrals = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->canInvite = false;
        $this->score = 0;
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
    public function setAccount(Account $account = null)
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
     * @param ArrayCollection $tickets
     * @return User
     */
    public function setTickets($tickets)
    {
        $this->tickets = $tickets;

        return $this;
    }

    /**
     * @return Ticket[]|ArrayCollection
     */
    public function getTickets()
    {
        return $this->tickets;
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

    /**
     * @param string $vkontakte
     * @return User
     */
    public function setVKontakte($vkontakte)
    {
        $this->vkontakte = $vkontakte;

        return $this;
    }

    /**
     * @return string
     */
    public function getVKontakte()
    {
        return $this->vkontakte;
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
     * @param User[]|ArrayCollection $referrals
     * @return User
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;

        return $this;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * @param int $level
     * @return User
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string $path
     * @return User
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * @param bool $value
     * @return User
     */
    public function setCanInvite($value)
    {
        $this->canInvite = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCanInvite()
    {
        return $this->canInvite;
    }

    /**
     * @param integer $score
     * @return User
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param integer $value
     * @return User
     */
    public function increaseScore($value)
    {
        $this->score += $value;

        return $this;
    }

    /**
     * @param Notification[]\ArrayCollection $notifications
     * @return User
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * @return Notification[]|ArrayCollection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}