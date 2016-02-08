<?php

namespace AppBundle\Tests\Service\PaymentSystem;

use AppBundle\Entity\Account;
use AppBundle\Entity\Ticket;
use AppBundle\Service\PaymentSystem\PerfectMoney;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Pool;

class PerfectMoneyTest extends TestCase
{
    private $account;

    private $entityManager;

    public function setUp()
    {
        $this->account = new Account();

        $repository = $this->getMockWithoutInvokingTheOriginalConstructor('AppBundle\\Entity\\AccountRepository');
        $repository->expects($this->once())->method('getPoorestSystemAccount')->willReturn($this->account);

        $this->entityManager = $this->getMockWithoutInvokingTheOriginalConstructor('Doctrine\\ORM\\EntityManager');
        $this->entityManager->expects($this->once())->method('getRepository')->willReturn($repository);
    }

    public function testCreatePaymentRequest()
    {
        $options = array(
            'status_url' => 'http://fixfin.dev/payments/perfectmoney/confirm',
            'payment_url' => 'http://fixfin.dev/payments/perfectmoney/success',
            'nopayment_url' => 'http://fixfin.dev/payments/perfectmoney/failed',
            'available_payment_methods' => 'account'
        );

        $pool = new Pool();

        $rate = new Rate();
        $rate->setPool($pool);

        $ticket = new Ticket();
        $ticket->setRate($rate);

        $invoice = new Invoice();
        $invoice->setTicket($ticket)
            ->setAmount(65.0);

        $perfectMoney = new PerfectMoney(new PropertyAccessor(), $this->entityManager, $options);
        $request = $perfectMoney->createPaymentRequest($invoice);

        $this->assertInstanceOf('PerfectMoneyBundle\\Model\\PaymentRequest', $request);
        $this->assertNull($request->getPaymentId());
        $this->assertEquals(65.0, $request->getPaymentAmount());
        $this->assertEquals($this->account, $request->getPayeeAccount());
        $this->assertEquals($options['status_url'], $request->getStatusUrl());
        $this->assertEquals($options['payment_url'], $request->getPaymentUrl());
        $this->assertEquals($options['nopayment_url'], $request->getNoPaymentUrl());
        $this->assertEquals($options['available_payment_methods'], $request->getAvailablePaymentMethods());
    }
}