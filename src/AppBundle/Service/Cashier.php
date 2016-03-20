<?php

namespace AppBundle\Service;

use AppBundle\Entity\MoneyTransaction;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\Invoice;
use AppBundle\Event\InvoiceEvent;
use AppBundle\Event\TicketEvent;
use AppBundle\Exception\DuplicateConfirmException;
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

        if ($rate->isRequireQualification()) {
            $qualification = $this->committee->create($ticket);
            $ticket->setQualification($qualification);
        }

        $this->entityManager->persist($ticket);

        $event = new TicketEvent($ticket);
        $this->dispatcher->dispatch(TicketEvent::CREATED, $event);

        return $ticket;
    }

    /**
     * @param User $user
     * @param Rate $rate
     * @param bool $andFlush
     * @return Invoice
     */
    public function createInvoice(User $user, Rate $rate, $andFlush = false)
    {
        $invoice = new Invoice();
        $invoice->setUser($user)
            ->setRate($rate)
            ->setAmount($rate->getAmount())
            ->setPeriod($rate->getPeriod())
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
     * @return Ticket|null
     */
    private function determineDirectChief(Ticket $ticket)
    {
        $referrer = $this->accessor->getValue($ticket, 'user.referrer');

        if (!$referrer) {
            return null;
        }

        $rate = $ticket->getRate();
        $refTicket = $this->entityManager->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $referrer);

        if ($refTicket instanceof Ticket) {

            if (!$this->checkTicketExpiration($refTicket)) {
                return null;
            }

            if (!$rate->isRequireQualification()) {
                $ticket->setChiefTicket($refTicket);

                return $refTicket;
            }

            $qualification = $refTicket->getQualification();

            if ($qualification) {
                if ($qualification->isPassed() || $this->committee->tryPass($qualification)) {
                    $ticket->setChiefTicket($refTicket);

                    return $refTicket;
                }
                // Добавляем тикет в квалификацию тикета реферрера.
                $qualification->addTransferredTicket($ticket);
            } else {
                $ticket->setChiefTicket($refTicket);

                 return $refTicket;
            }
        }

        return null;
    }

    /**
     * @param Ticket $ticket
     * @return Ticket|null
     * @todo: Необходимо написать тесты для этого метода.
     */
    public function determineChainChief(Ticket $ticket)
    {
        $referrers = $this->entityManager->getRepository('AppBundle:User')->getReferrers($ticket);

        if (count($referrers)) {
            $closestTicket = $this->entityManager->getRepository('AppBundle:Ticket')
                ->getClosestTicket($ticket, $referrers);

            if ($closestTicket) {
                $ticket->setChiefTicket($closestTicket);

                return $closestTicket;
            }
        }

        return null;
    }

    /**
     * @param User $user
     * @param Rate $rate
     * @return Invoice
     */
    public function getInvoiceForPayment(User $user, Rate $rate)
    {
        $invoice = $this->entityManager->getRepository('AppBundle:Invoice')
            ->getActualInvoice($user, $rate);

        if ($invoice instanceof Invoice && $this->checkInvoiceExpiration($invoice)) {
            return $invoice;
        }

        return $this->createInvoice($user, $rate);
    }

    /**
     * @param Invoice $invoice
     * @return Ticket
     */
    public function getTicketForProlongation(Invoice $invoice)
    {
        $ticket = $this->entityManager->getRepository('AppBundle:Ticket')
            ->getTicketByRate($invoice->getRate(), $invoice->getUser());

        if ($ticket instanceof Ticket) {
            return $ticket;
        }

        return $this->createTicket($invoice->getUser(), $invoice->getRate());
    }

    /**
     * @param Invoice $invoice
     */
    private function processInvoicePaid(Invoice $invoice, $flush = true)
    {
        if ($this->checkInvoiceFullyPaid($invoice)) {
            // Получаем уже существующий тикет или создаем новый.
            $ticket = $this->getTicketForProlongation($invoice);

            // Назначаем инвойсу тикет за который была произведена оплата.
            $invoice->setTicket($ticket);

            // Тут дальше идет логика по обработки тикета и связанных c ним тикетов.
            $this->prolongationTicket($ticket);
            $this->reestablishChief($ticket);
            $this->reestablishSubordinates($ticket);

            $chiefTicket = $ticket->getChiefTicket();

            if ($chiefTicket instanceof Ticket && $chiefTicket->isExpired() == false) {
                $transaction = $this->banker->createRewardTransaction($chiefTicket);
                $this->banker->processRewardTransaction($transaction);
            }
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

        // Issue-41 поспособствовало этому куску кода.
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

        if ($chiefTicket && $this->checkTicketExpiration($chiefTicket)) {
            return;
        }

        if ($this->determineDirectChief($ticket)) {
            // Кидаем событие о том что chief тикет назначен от прямого лидера.
            $this->dispatcher->dispatch(TicketEvent::CHIEF_REESTABLISHED, new TicketEvent($ticket));

            return;
        }

        // Пытаемся определить chief тикет по цепочки реферреров.
        if ($this->determineChainChief($ticket)) {
            // Кидаем событие о том что chief тикет назначен из цепочки.
            $this->dispatcher->dispatch(TicketEvent::CHIEF_REESTABLISHED, new TicketEvent($ticket));

            // Выходим до анулирования только что назначенного chief тикета.
            return;
        }

        // Если нет подходящего тикета то анулируем chief тикет у текущего тикета.
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

        // Нам обязательно нужен инвойс.
        if (!$invoice) {
            throw new NoResultException();
        }

        // Устанавливаем количество оплаты.
        $invoice->setPaid($payment->getPaymentAmount());
        $this->processInvoicePaid($invoice, false);

        $transaction = $this->banker->createProlongTransaction($invoice, $payeeAccount);
        $transaction->setStatus(MoneyTransaction::STATUS_DONE)
            ->setExternal($payment->getPaymentBatchNum());

        $this->entityManager->flush();

        return $transaction;
    }
}