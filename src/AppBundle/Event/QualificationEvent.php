<?php

namespace AppBundle\Event;

use AppBundle\Entity\Qualification;
use Symfony\Component\EventDispatcher\Event;

class QualificationEvent extends Event
{
    /** @var Qualification */
    private $qualification;

    /**
     * @param Qualification $qualification
     */
    public function __construct(Qualification $qualification)
    {
        $this->qualification = $qualification;
    }

    /**
     * @return Qualification
     */
    public function getQualification()
    {
        return $this->qualification;
    }
}