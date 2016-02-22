<?php

namespace AppBundle\Model;

use AppBundle\Entity\MoneyTransaction;

class RewardResult
{
    /**
     * @var MoneyTransaction[]
     */
    private $completed;

    /**
     * @var MoneyTransaction[]
     */
    private $repeated;

    /**
     * @var MoneyTransaction[]
     */
    private $errors;

    public function __construct()
    {
        $this->completed = array();
        $this->repeated = array();
        $this->errors = array();
    }

    /**
     * @param MoneyTransaction $transaction
     * @return RewardResult
     */
    public function addComplete(MoneyTransaction $transaction)
    {
        $this->completed[spl_object_hash($transaction)] = $transaction;

        return $this;
    }

    /**
     * @param MoneyTransaction $transaction
     * @return MoneyTransaction[]
     */
    public function getCompleted(MoneyTransaction $transaction)
    {
        return $this->completed;
    }

    /**
     * @param MoneyTransaction $transaction
     * @return RewardResult
     */
    public function addRepeat(MoneyTransaction $transaction)
    {
        $this->repeated[spl_object_hash($transaction)] = $transaction;

        return $this;
    }

    /**
     * @return MoneyTransaction[]
     */
    public function getRepeated()
    {
        return $this->repeated;
    }

    /**
     * @param MoneyTransaction $transaction
     * @return RewardResult
     */
    public function addError(MoneyTransaction $transaction)
    {
        $this->errors[spl_object_hash($transaction)] = $transaction;

        return $this;
    }

    /**
     * @return MoneyTransaction[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return float
     */
    public function getRewarded()
    {
        $result = 0.0;

        foreach ($this->completed as $item) {
            $result += $item->getAmount();
        }

        return $result;
    }

    /**
     * @return float
     */
    public function getReserved()
    {
        $result = 0.0;

        foreach ($this->repeated as $item) {
            $result += $item->getAmount();
        }

        return $result;
    }

    /**
     * @return float
     */
    public function getReleased()
    {
        $result = 0.0;

        foreach ($this->errors as $item) {
            $result += $item->getAmount();
        }

        return $result;
    }
}