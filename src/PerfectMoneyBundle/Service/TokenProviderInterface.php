<?php

namespace PerfectMoneyBundle\Service;

interface TokenProviderInterface
{
    public function getToken($number);
}