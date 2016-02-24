<?php

namespace AppBundle\Service;

use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Qualification;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\Invoice;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Event\TicketEvent;
use AppBundle\Exception\DuplicateConfirmException;
use AppBundle\Exception\ScoreRuleException;
use Doctrine\ORM\NoResultException;
use PerfectMoneyBundle\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Cashier
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Committee */
    private $committee;

    /** @var Banker */
    private $banker;

    /** @var PropertyAccessor */
    private $accessor;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, Committee $committee, Banker $banker, PropertyAccessor $accessor)
    {
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->committee = $committee;
        $this->banker = $banker;
        $this->accessor = $accessor;
    }

    /**
     * @param User $user
     * @param Rate $rate
     * @return Ticket
     */
    public function createTicket(User $user, Rate $rate)
    {
        $ticket = new Ticket();
        $ticket->setRate($rate)
            ->setUser($user);

        $this->entityManager->persist($ticket);

        $event = new TicketEvent($ticket);
        $this->dispatcher->dispatch(TicketEvent::CREATED, $event);

        return $ticket;
    }

    /**
     * @param Ticket $ticket
     * @return Invoice
     */
    public function createInvoice(Ticket $ticket, $andFlush = false)
    {
        $amount = $this->accessor->getValue($ticket, 'rate.amount');
        $period = $this->accessor->getValue($ticket, 'rate.period');

        $invoice = new Invoice();
        $invoice->setTicket($ticket)
            ->setAmount($amount)
            ->setPeriod($period)
            ->setExpiredAt(new \DateTime('+1 week')); //TODO: потом желательно этот хардкод убрать

        $this->entityManager->persist($invoice);

        if ($andFlush) {
            $this->entityManager->flush();
        }

        $event = new InvoiceEvent($invoice);
        $this->dispatcher->dispatch(InvoiceEvent::CREATED, $event);

        return $invoice;
    }

    /**
     * @param Ticket $ticket
     * @return bool
     */
    private function checkTicketExpiration(Ticket $ticket)
    {
        if ($ticket->isOwnership()) {
            $ticket->setExpired(false);

            return true;
        }

        $paidUp = $ticket->getPaidUp();

        if (!$paidUp instanceof \DateTime) {
            $ticket->setExpired(true);

            return false;
        }

        $now = new \DateTime();

        if ($now->getTimestamp() > $paidUp->getTimestamp()) {
            $ticket->setExpired(true);

            return false;
        }

        $ticket->setExpired(false);

        return true;
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    private function checkInvoiceExpiration(Invoice $invoice)
    {
        $now = new \DateTime();
        $expiredAtTimestamp = $this->accessor->getValue($invoice, 'expiredAt.timestamp');

        if ($now->getTimestamp() > $expiredAtTimestamp) {
            $invoice->setStatus(Invoice::STATUS_EXPIRED);

            return false;
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    private function checkInvoiceFullyPaid(Invoice $invoice)
    {
        if ($invoice->getAmount() > $invoice->getPaid()) {
            $invoice->setStatus(Invoice::STATUS_PARTIAL_PAID);

            return false;
        }

        $invoice->setStatus(Invoice::STATUS_PAID);

        return true;
    }

    /**
     * @param Ticket $ticket
     */
    private function determineChiefTicket(Ticket $ticket)
    {
        $referrer = $this->accessor->getValue($ticket, 'user.referrer');
        $rate = $ticket->getRate();

        if (!$referrer instanceof User) {
            return;
        }

        $refTicket = $this->entityManager->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $referrer);

        if ($refTicket instanceof Ticket) {
            if (!$rate->isRequireQualification()) {
                $ticket->setChiefTicket($refTicket);

                return;
            }

            $qualification = $refTicket->getQualification();

            if ($qualification) {
                if ($qualification->isPassed() || $this->committee->tryPass($qualification)) {
                    $ticket->setChiefTicket($refTicket);

                    return;
                }
            } else {
                $qualification = $this->committee->create($refTicket);
                $refTicket->setQualification($qualification);
            }

            $qualification->addTransferredTicket($ticket);
        }

        // Если попытка назначения chief ticket из реферрера провалилась тогда пробуем назначить подходящий такет восходя по ветке рефераллов
        $this->determineClosestChiefTicket($ticket);
    }

    /**
     * @param Ticket $ticket
     * @return Ticket|null
     * @todo: Необходимо написать тесты для этого метода.
     */
    public function determineClosestChiefTicket(Ticket $ticket)
    {
        $referrers = $this->entityManager->getRepository('AppBundle:User')
            ->getReferrers($ticket);

        if (count($referrers)) {
            $closestTicket = $this->entityManager
                ->getRepository('AppBundle:Ticket')
                ->getClosestTicket($ticket, $referrers);

            $ticket->setChiefTicket($closestTicket);

            return $closestTicket;
        }

        return null;
    }

    /**
     * @param User $user
     * @param Rate $rate
     * @param bool $flush
     * @return Ticket
     */
    public function openTable(User $user, Rate $rate, $flush = true)
    {
        $ticket = $this->createTicket($user, $rate);

        if ($rate->isRequireQualification()) {
            $qualification = $this->committee->create($ticket);
            $ticket->setQualification($qualification);
        }

        $this->determineChiefTicket($ticket);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $ticket;
    }

    /**
     * @param Ticket $ticket
     * @return Invoice|mixed
     */
    public function getInvoiceForProlongation(Ticket $ticket)
    {
        $invoice = $this->entityManager->getRepository('AppBundle:Invoice')
            ->getActualInvoice($ticket);

        if ($invoice instanceof Invoice && $this->checkInvoiceExpiration($invoice)) {
            return $invoice;
        }

        return $this->createInvoice($ticket, true);
    }

    /**
     * @param Invoice $invoice
     */
    private function processInvoicePaid(Invoice $invoice, $flush = true)
    {
        if ($this->checkInvoiceFullyPaid($invoice)) {
            $ticket = $invoice->getTicket();

            // Тут дальше идет логика по обработки тикета и связанных c ним тикетов.
            $this->prolongationTicket($ticket);
            $this->reestablishChief($ticket);
            $this->reestablishSubordinates($ticket);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Ticket $ticket
     * @return Ticket
     */
    private function prolongationTicket(Ticket $ticket)
    {
        $period = $this->accessor->getValue($ticket, 'rate.period');

        $paidInterval = new \DateInterval('P'.$period.'D');
        $now = new \DateTime();

        $paidUp = $ticket->getPaidUp();
        if (!$paidUp instanceof \DateTime || ($paidUp->getTimestamp() < $now->getTimestamp())) {
            $paidUp = $now;
        }

        // Issue-41 поспособствовало этому куску кода
        $newPaidUp = clone $paidUp;
        $ticket->setPaidUp($newPaidUp->add($paidInterval))
            ->setExpired(false);

        return $ticket;
    }

    /**
     * @param Ticket $ticket
     */
    private function reestablishChief(Ticket $ticket)
    {
        $chiefTicket = $ticket->getChiefTicket();

        if (!$chiefTicket) {
            return;
        }

        if ($this->checkTicketExpiration($chiefTicket)) {
            $this->banker->createRewardTransaction($chiefTicket);

            return;
        }

        // Получаем цепочку реферреров у которых есть оплаченные тикеты для данного стола
        $referrers = $this->entityManager->getRepository('AppBundle:User')
                    ->getReferrers($ticket);

        if (count($referrers)) {

            // Получаем ближний тикет для данного стола из указанной цепочки реферреров
            $closestTicket = $this->entityManager->getRepository('AppBundle:Ticket')
                ->getClosestTicket($ticket, $referrers);

            if ($closestTicket) {
                $ticket->setChiefTicket($closestTicket);

                // Кидаем событие о том что chief тикет назначен из цепочки
                $this->dispatcher->dispatch(TicketEvent::CHIEF_REESTABLISHED, new TicketEvent($ticket));
                $this->banker->createRewardTransaction($closestTicket);

                // Выходим до анулирования только что назначенного chief тикета
                return;
            }
        }

        // Если нет подходящего тикета то анулируем chief тикет у текущего тикета
        $ticket->setChiefTicket(null);
        $this->dispatcher->dispatch(TicketEvent::CHIEF_REMOVED, new TicketEvent($ticket));
    }

    /**
     * @param Ticket $ticket
     */
    private function reestablishSubordinates(Ticket $ticket)
    {
        $repository = $this->entityManager->getRepository('AppBundle:Ticket');

        if ($ticket->getQualification()) {
            $lostTickets = $repository->getLostReferralsTickets($ticket);
        } else {
            $lostTickets = $repository->getLostReferralsTickets($ticket, false);
        }

        if (!count($lostTickets)) {
            return;
        }

        foreach ($lostTickets as $subordinate) {
            $subordinate->setChiefTicket($ticket);
        }

        $this->dispatcher->dispatch(TicketEvent::SUBORDINATES_REESTABLISHED, new TicketEvent($ticket));
    }

    public function createProlongPaymentRequest(Ticket $ticket)
    {
        $invoice = $this->getInvoiceForProlongation($ticket);

        return $this->banker->createPaymentRequest($invoice);
    }

    /**
     * @param PaymentInterface $payment
     * @throws NoResultException
     * @throws DuplicateConfirmException
     * @return MoneyTransaction|null
     */
    public function handlePayment(PaymentInterface $payment)
    {
        $invoice = $this->entityManager->getRepository('AppBundle:Invoice')
            ->find($payment->getPaymentId());

        if ($invoice->getStatus() === Invoice::STATUS_PAID) {
            throw new DuplicateConfirmException('Получен дубликат подтверждения об оплате');
        }

        $payeeAccount = $this->entityManager->getRepository('AppBundle:Account')
            ->getAccountByNumber($payment->getPayeeAccount());

        // Нам обязательно нужен инвойс
        if (!$invoice) {
            throw new NoResultException();
        }

        // Устанавливаем количество оплаты
        $invoice->setPaid($payment->getPaymentAmount());
        $this->processInvoicePaid($invoice, false);

        $transaction = $this->banker->createProlongTransaction($invoice, $payeeAccount);
        $transaction->setStatus(MoneyTransaction::STATUS_DONE)
            ->setExternal($payment->getPaymentBatchNum());

        $this->entityManager->flush();

        return $transaction;
    }
}