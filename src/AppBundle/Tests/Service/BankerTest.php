<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\Account;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Pool;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\User;
use AppBundle\Entity\Rate;
use AppBundle\Service\Banker;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class BankerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $entityManager;

    /** @var Banker */
    private $banker;

    /** @var Account */
    private $payerAccount;

    /** @var Account */
    private $payeeAccount;

    /** @var float */
    private $amount;

    public function setUp()
    {
        $this->entityManager = $this->getMockWithoutInvokingTheOriginalConstructor('Doctrine\\ORM\\EntityManager');
        $this->entityManager->expects($this->any())->method('persist');

        $accessor = new PropertyAccessor();
        $perfectMoney = $this->getMockWithoutInvokingTheOriginalConstructor('AppBundle\\Service\\PaymentSystem\\PerfectMoney');

        $this->banker = new Banker($this->entityManager, $accessor, $perfectMoney);

        $this->payerAccount = new Account();
        $this->payeeAccount = new Account();
        $this->amount = 165.0;
    }

    public function testCreateProlongTransaction()
    {
        $user = new User();
        $user->setAccount($this->payerAccount);

        $ticket = new Ticket();
        $ticket->setUser($user);

        $invoice = new Invoice();
        $invoice->setPaid($this->amount)
            ->setTicket($ticket);

        $transaction = $this->banker->createProlongTransaction($invoice, $this->payeeAccount);

        $this->assertInstanceOf('AppBundle\\Entity\\MoneyTransaction', $transaction);
        $this->assertEquals($this->payeeAccount, $transaction->getDestination());
        $this->assertEquals($this->payerAccount, $transaction->getSource());
        $this->assertEquals($this->amount, $transaction->getAmount());
        $this->assertEquals(MoneyTransaction::TYPE_PROLONG, $transaction->getType());
        $this->assertEquals(MoneyTransaction::STATUS_NEW, $transaction->getStatus());
        $this->assertEquals($invoice, $transaction->getInvoice());
    }

    public function testCreateRewardTransaction()
    {
        $user = new User();
        $user->setAccount($this->payeeAccount);

        $pool = new Pool();
        $rate = new Rate();
        $rate->setPool($pool)
            ->setCommission(20.0)
            ->setAmount($this->amount);

        $ticket = new Ticket();
        $ticket->setUser($user)
            ->setRate($rate);

        $repository = $this->getMockWithoutInvokingTheOriginalConstructor('AppBundle\\Entity\\AccountRepository');
        $repository->expects($this->once())
            ->method('getWealthSystemAccount')
            ->willReturn($this->payerAccount);

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        $transaction = $this->banker->createRewardTransaction($ticket);

        $this->assertInstanceOf('AppBundle\\Entity\\MoneyTransaction', $transaction);
        $this->assertEquals($this->payeeAccount, $transaction->getDestination());
        $this->assertEquals($this->payerAccount, $transaction->getSource());
        $this->assertEquals(132.0, $transaction->getAmount());
        $this->assertEquals(MoneyTransaction::TYPE_REWARD, $transaction->getType());
        $this->assertEquals(MoneyTransaction::STATUS_NEW, $transaction->getStatus());
        $this->assertNull($transaction->getInvoice());
    }

    public function testCreateWithdrawalTransaction()
    {
        $transaction = $this->banker->createWithdrawalTransaction($this->payeeAccount, $this->payerAccount, $this->amount);

        $this->assertInstanceOf('AppBundle\\Entity\\MoneyTransaction', $transaction);
        $this->assertEquals($this->payeeAccount, $transaction->getDestination());
        $this->assertEquals($this->payerAccount, $transaction->getSource());
        $this->assertEquals($this->amount, $transaction->getAmount());
        $this->assertEquals(MoneyTransaction::TYPE_WITHDRAWAL, $transaction->getType());
        $this->assertEquals(MoneyTransaction::STATUS_NEW, $transaction->getStatus());
        $this->assertNull($transaction->getInvoice());
    }
}