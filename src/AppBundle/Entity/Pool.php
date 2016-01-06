<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pool
 */
class Pool
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var Account */
    private $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
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
     * @return Pool
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
     * @return Account|ArrayCollection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    public function addAccount(Account $account)
    {
        $this->accounts->add($account);

        return $this;
    }
}

