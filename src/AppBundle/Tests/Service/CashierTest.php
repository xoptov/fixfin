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
        $rate = new Rate();
        $rate->setAmount(20)
            ->setPeriod(30);

        $ticket = new Ticket();
        $ticket->setRate($rate);

        $this->mockDispatcher->expects($this->exactly(2))->method('dispatch')->withAnyParameters();
        $this->mockEntityManager->expects($this->exactly(2))->method('persist')->withAnyParameters();

        $accessor = new PropertyAccessor();

        $cashier = new Cashier($this->mockDispatcher, $this->mockEntityManager, $this->mockCommittee, $this->mockBanker, $accessor);
        $invoice = $cashier->createInvoice($ticket);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($ticket, $invoice->getTicket());
        $this->assertAttributeSame($rate->getAmount(), 'amount', $invoice);
        $this->assertAttributeSame($rate->getPeriod(), 'period', $invoice);

        $expiredDate = new \DateTime('+1 week');
        $this->assertAttributeEquals($expiredDate, 'expiredAt', $invoice);

        $this->mockEntityManager->expects($this->once())->method('flush')->withAnyParameters();
        $cashier->createInvoice($ticket, true);

    }

    private function prepareOpenTableServices()
    {
        self::bootKernel();

        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $accessor = new PropertyAccessor();
        $committee = new Committee($entityManager, $accessor);
        $cashier = new Cashier($this->mockDispatcher, $entityManager, $committee, $this->mockBanker, $accessor);

        return array('cashier' => $cashier, 'entityManager' => $entityManager);
    }

    // Ну тут проверка на правильное создание тикета для стола
    public function testOpenTableForValidCreatingTicket()
    {
        $services = $this->prepareOpenTableServices();

        $services['entityManager']->beginTransaction();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'xoptov'));

        /** @var Rate $rate */
        $rate = $services['entityManager']
            ->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $ticket = $services['cashier']->openTable($user, $rate);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertAttributeEquals($user, 'user', $ticket);
        $this->assertAttributeEquals($rate, 'rate', $ticket);
        $this->assertInstanceOf(Qualification::class, $ticket->getQualification());
        $this->assertAttributeEquals($rate->getRequireInvitation(), 'requireInvitation', $ticket->getQualification());
        $this->assertNull($ticket->getChiefTicket());

        $services['entityManager']->rollback();
    }

    // Далее проверяем привязку тикета по ирархической цепочке если нельзя привязаться к тикету прямого лидера
    public function testOpenTableForChainChiefTicket()
    {
        $services = $this->prepareOpenTableServices();

        $services['entityManager']->beginTransaction();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'dasha'));

        /** @var Rate $rate */
        $rate = $services['entityManager']
            ->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $chainReferrer = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'alex'));

        $ticket = $services['cashier']->openTable($user, $rate);

        $this->assertAttributeInstanceOf(Ticket::class, 'chiefTicket', $ticket);
        $this->assertEquals($chainReferrer, $ticket->getChiefTicket()->getUser());

        $services['entityManager']->rollback();
    }

    // Проверка на добавление в квалификацию тикета
    public function testOpenTableForAppendQualification()
    {
        $services = $this->prepareOpenTableServices();

        $services['entityManager']->beginTransaction();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'oleg'));

        /** @var Rate $rate */
        $rate = $services['entityManager']
            ->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $directTicket = $services['entityManager']
            ->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $user->getReferrer());

        $ticket = $services['cashier']->openTable($user, $rate);

        $this->assertAttributeEmpty('chiefTicket', $ticket);
        $this->assertAttributeInstanceOf(Qualification::class, 'qualification', $directTicket);
        $this->assertContains($ticket, $directTicket->getQualification()->getTransferredTickets());

        $services['entityManager']->rollback();
    }

    // Проверка на привязку тикета к тикету реферерра с пройденной квалификацией
    public function testOpenTableForDirectChiefTicket()
    {
        $services = $this->prepareOpenTableServices();

        $services['entityManager']->beginTransaction();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'misha'));

        /** @var Rate $rate */
        $rate = $services['entityManager']
            ->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $directTicket = $services['entityManager']
            ->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $user->getReferrer());

        $ticket = $services['cashier']->openTable($user, $rate);

        $this->assertAttributeInstanceOf(Ticket::class, 'chiefTicket', $ticket);
        $this->assertAttributeEquals($directTicket, 'chiefTicket', $ticket);
        $this->assertNotContains($ticket, $directTicket->getQualification()->getTransferredTickets());

        $services['entityManager']->rollback();
    }

    private function prepareHandlePaymentServices()
    {
        self::bootKernel();

        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $accessor = new PropertyAccessor();
        $banker = self::$kernel->getContainer()->get('app.banker_service');
        $cashier = new Cashier($this->mockDispatcher, $entityManager, $this->mockCommittee, $banker, $accessor);

        return array(
            'entityManager' => $entityManager,
            'cashier' => $cashier
        );
    }

    // Тут тестируется только продление тикета которые несодержит приглашенных людей в структуру и сам без лидера.
    public function testHandlePaymentFromKostan()
    {
        $services = $this->prepareHandlePaymentServices();

        $services['entityManager']->beginTransaction();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'kostan'));

        /** @var Rate $rate */
        $rate = $services['entityManager']
            ->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $ticket = $services['entityManager']
            ->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $user);

        $payeeAccount = $services['entityManager']
            ->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($rate->getPool());

        /** @var Invoice $invoice */
        $invoice = $services['entityManager']
            ->getRepository('AppBundle:Invoice')
            ->getActualInvoice($ticket);

        $paymentConfirm = new PaymentConfirmation();
        $paymentConfirm
            ->setPayeeAccount($payeeAccount->getNumber())
            ->setPaymentAmount($invoice->getAmount())
            ->setPaymentId($invoice->getId())
            ->setPaymentBatchNum(rand(1000000, 9999999));

        $transaction = $services['cashier']->handlePayment($paymentConfirm);

        $this->assertInstanceOf(MoneyTransaction::class, $transaction);

        $services['entityManager']->rollback();
    }

    // Тут тестируется полный процесс с привязкой тикетов рефераллов если они невходят в квалификацию.
    public function testHandlePaymentFromDima()
    {
        $services = $this->prepareHandlePaymentServices();

        $services['entityManager']->beginTransaction();

        $user = $services['entityManager']
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => 'dima'));

        /** @var Rate $rate */
        $rate = $services['entityManager']
            ->getRepository('AppBundle:Rate')
            ->getCheapestRate();

        $ticket = $services['entityManager']
            ->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $user);

        $payeeAccount = $services['entityManager']
            ->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($rate->getPool());

        /** @var Invoice $invoice */
        $invoice = $services['entityManager']
            ->getRepository('AppBundle:Invoice')
            ->getActualInvoice($ticket);

        $paymentConfirm = new PaymentConfirmation();
        $paymentConfirm
            ->setPayeeAccount($payeeAccount->getNumber())
            ->setPaymentAmount($invoice->getAmount())
            ->setPaymentId($invoice->getId())
            ->setPaymentBatchNum(rand(1000000, 9999999));

        $transaction = $services['cashier']->handlePayment($paymentConfirm);

        $this->assertInstanceOf(MoneyTransaction::class, $transaction);

        $services['entityManager']->rollback();
    }
}