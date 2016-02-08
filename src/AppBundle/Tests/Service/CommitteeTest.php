<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Rate;
use AppBundle\Entity\Ticket;
use AppBundle\Service\Committee;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Entity\Qualification;

class CommitteeTest extends TestCase
{
    private $entityManager;

    /** @var PropertyAccessor */
    private $accessor;

    /** @var Committee */
    private $committee;

    public function setUp()
    {
        $this->entityManager = $this->getMockWithoutInvokingTheOriginalConstructor('Doctrine\\ORM\\EntityManager');

        $this->accessor = new PropertyAccessor();
        $this->committee = new Committee($this->entityManager, $this->accessor);
    }

    public function testCreate()
    {
        $this->entityManager->expects($this->once())->method('persist');

        $rate = new Rate();
        $rate->setRequireInvitation(2);

        $ticket = new Ticket();
        $ticket->setRate($rate);

        $qualification = $this->committee->create($ticket);

        $this->assertInstanceOf('AppBundle\\Entity\\Qualification', $qualification);
        $this->assertEquals($qualification->getRequireInvitation(), 2);
        $this->assertEquals($qualification->getTicket(), $ticket);
        $this->assertFalse($qualification->isPassed());
    }

    public function testTryPassTrue()
    {
        $qualification = new Qualification();
        $qualification->setRequireInvitation(2)
            ->addTransferredTicket(new Ticket())
            ->addTransferredTicket(new Ticket());

        $this->assertTrue($this->committee->tryPass($qualification));
        $this->assertTrue($qualification->isPassed());
    }

    public function testTryPassFalse()
    {
        $qualification = new Qualification();
        $qualification->setRequireInvitation(2);

        $this->assertFalse($this->committee->tryPass($qualification));
        $this->assertFalse($qualification->isPassed());
    }

}