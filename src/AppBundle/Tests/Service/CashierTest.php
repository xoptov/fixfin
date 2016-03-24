<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Ticket;
use AppBundle\Service\Cashier;
use Doctrine\ORM\EntityManager;
use PerfectMoneyBundle\Model\PaymentConfirmation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Service\Committee;
use AppBundle\Service\Banker;
use AppBundle\Entity\User;
use AppBundle\Entity\Qualification;

class CashierTest extends KernelTestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockDispatcher;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockEntityManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockCommittee;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockBanker;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $mockAccessor;

    public function setUp()
    {
        $this->mockDispatcher = $this->getMockWithoutInvokingTheOriginalConstructor(EventDispatcher::class);
        $this->mockEntityManager = $this->getMockWithoutInvokingTheOriginalConstructor(EntityManager::class);
        $this->mockCommittee = $this->getMockWithoutInvokingTheOriginalConstructor(Committee::class);
        $this->mockBanker = $this->getMockWithoutInvokingTheOriginalConstructor(Banker::class);
        $this->mockAccessor = $this->getMockWithoutInvokingTheOriginalConstructor(PropertyAccessor::class);
    }

    public function testCreateTicket()
    {
        $this->mockDispatcher->expects($this->once())->method('dispatch')->withAnyParameters();
        $this->mockEntityManager->expects($this->once())->method('persist')->withAnyParameters();

        $cashier = new Cashier($this->mockDispatcher, $this->mockEntityManager, $this->mockCommittee, $this->mockBanker, $this->mockAccessor);

        $user = new User();
        $rate = new Rate();

        $ticket = $cashier->createTicket($user, $rate);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals($user, $ticket->getUser());
        $this->assertEquals($rate, $ticket->getRate());
    }

    public function testCreateInvoice()
    {
        $services = $this->prepareHandlePaymentServices();
        $services['entityManager']->beginTransaction();

        $rate = $services['entityManager']->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneByUsername('dasha');

        $this->mockDispatcher->expects($this->exactly(1))->method('dispatch')->withAnyParameters();

        $invoice = $services['cashier']->createInvoice($user, $rate);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertAttributeSame($rate->getAmount(), 'amount', $invoice);
        $this->assertAttributeSame($rate->getPeriod(), 'period', $invoice);

        $expiredDate = new \DateTime('+1 week');
        $this->assertAttributeEquals($expiredDate, 'expiredAt', $invoice);

        $services['entityManager']->rollback();
    }

    private function prepareHandlePaymentServices()
    {
        self::bootKernel();

        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $accessor = new PropertyAccessor();
        $banker = self::$kernel->getContainer()->get('app.banker_service');
        $committee = self::$kernel->getContainer()->get('app.committee_service');
        $cashier = new Cashier($this->mockDispatcher, $entityManager, $committee, $banker, $accessor);

        return array(
            'entityManager' => $entityManager,
            'cashier' => $cashier
        );
    }

    public function testProlongationTicket()
    {
        $now = new \DateTime();

        $rate = new Rate();
        $rate->setPeriod(7);

        $ticket = new Ticket();
        $ticket->setRate($rate);

        $cashier = new Cashier($this->mockDispatcher, $this->mockEntityManager, $this->mockCommittee, $this->mockBanker, new PropertyAccessor());

        $reflection = new \ReflectionClass(get_class($cashier));

        $method = $reflection->getMethod('prolongationTicket');
        $method->setAccessible(true);

        // Сначала тестируем просроченный тикет
        $paidUp = clone $now;
        $paidUp->modify('-10 days');
        $ticket->setPaidUp($paidUp);

        $target = clone $now;
        $target->modify('+7 days');

        $method->invoke($cashier, $ticket);
        $this->assertEquals($target->getTimestamp(), $ticket->getPaidUp()->getTimestamp());

        // Теперь тестируем не просроченный тикет
        $paidUp = clone $now;
        $paidUp->modify('+10 days');
        $ticket->setPaidUp($paidUp);

        $target = clone $now;
        $target->modify('+17 days');

        $method->invoke($cashier, $ticket);
        $this->assertEquals($target->getTimestamp(), $ticket->getPaidUp()->getTimestamp());
    }

    public function testForIssue41()
    {
        $services = $this->prepareHandlePaymentServices();

        $services['entityManager']->beginTransaction();

        $paymentConfirm = new PaymentConfirmation();
        $paymentConfirm->setPayerAccount('U8283472')
            ->setPayeeAccount('U9102389')
            ->setPaymentAmount(0.2)
            ->setPaymentBatchNum(rand(1000000, 9999999))
            ->setPaymentId(3);

        $services['cashier']->handlePayment($paymentConfirm);

        $services['entityManager']->rollback();
    }
}