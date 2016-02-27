<?php

namespace AppBundle\Entity;

class Notification
{
    /** @var int */
    private $id;

    /** @var User */
    private $user;

    /** @var int */
    private $type;

    /** @var string */
    private $content;

    /** @var bool */
    private $viewed;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    const TYPE_COMMON = 0;
    const TYPE_NEW_REFERRAL = 1;
    const TYPE_REFERRAL_TABLE_OPENED = 2;
    const TYPE_TICKET_EXPIRED = 3;
    const TYPE_REWARDING = 4;

    public function __construct()
    {
        $this->type = self::TYPE_COMMON;
        $this->viewed = false;
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
     * @param User $user
     * @return Notification
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
     * @param int $type
     * @return Notification
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
     * @param string $content
     * @return Notification
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param bool $value
     * @return Notification
     */
    public function setViewed($value)
    {
        $this->viewed = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isViewed()
    {
        return $this->viewed;
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
}