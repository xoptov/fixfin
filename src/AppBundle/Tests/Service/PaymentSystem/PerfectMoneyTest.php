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
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\AccountRepository;
use Monolog\Logger;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PerfectMoneyTest extends TestCase
{
    private $account;

    private $entityManager;

    private $router;

    private $logger;

    public function setUp()
    {
        $this->account = new Account();

        $repository = $this->getMockWithoutInvokingTheOriginalConstructor(AccountRepository::class);
        $repository->expects($this->once())->method('getPoorestSystemAccount')->willReturn($this->account);

        $this->entityManager = $this->getMockWithoutInvokingTheOriginalConstructor(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getRepository')->willReturn($repository);

        $this->router = $this->getMockWithoutInvokingTheOriginalConstructor(Router::class);
        $this->logger = $this->getMockWithoutInvokingTheOriginalConstructor(Logger::class);
    }

    public function testCreatePaymentRequest()
    {
        $options = array(
            'entry_form' => 'https://perfectmoney.is/api/step1.asp',
            'transfer_url' => 'https://perfectmoney.is/acct/confirm.asp',
            'routes' => array(
                'status' => 'perfect_money_confirm',
                'payment' => 'perfect_money_success',
                'no_payment' => 'perfect_money_failed'
            ),
            'available_payment_methods' => 'account'
        );

        $this->router->expects($this->at(0))->method('generate')
            ->with($options['routes']['status'], [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($options['routes']['status']);

        $this->router->expects($this->at(1))->method('generate')
            ->with($options['routes']['payment'], [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($options['routes']['payment']);

        $this->router->expects($this->at(2))->method('generate')
            ->with($options['routes']['no_payment'], [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($options['routes']['no_payment']);


        $pool = new Pool();

        $rate = new Rate();
        $rate->setPool($pool);

        $ticket = new Ticket();
        $ticket->setRate($rate);

        $invoice = new Invoice();
        $invoice->setTicket($ticket)
            ->setAmount(65.0);

        $perfectMoney = new PerfectMoney(new PropertyAccessor(), $this->entityManager, $this->router, $this->logger, $options);
        $request = $perfectMoney->createPaymentRequest($invoice);

        $this->assertInstanceOf('PerfectMoneyBundle\\Model\\PaymentRequest', $request);
        $this->assertNull($request->getPaymentId());
        $this->assertEquals(65.0, $request->getPaymentAmount());
        $this->assertEquals($this->account, $request->getPayeeAccount());
        $this->assertEquals($options['routes']['status'], $request->getStatusUrl());
        $this->assertEquals($options['routes']['payment'], $request->getPaymentUrl());
        $this->assertEquals($options['routes']['no_payment'], $request->getNoPaymentUrl());
        $this->assertEquals($options['available_payment_methods'], $request->getAvailablePaymentMethods());
    }
}