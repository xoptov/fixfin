<?php

namespace PerfectMoneyBundle\Model;

interface ResponseInterface
{
    public function setError($error);

    public function getError();
}