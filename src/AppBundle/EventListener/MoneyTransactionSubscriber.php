<?php

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Account;
use AppBundle\Entity\MoneyTransaction;

class MoneyTransactionSubscriber implements EventSubscriber
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UnitOfWork */
    private $uow;

    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush => 'onFlush'
        );
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $this->entityManager = $event->getEntityManager();
        $this->uow = $this->entityManager->getUnitOfWork();

        // Обрабатываем новые сущьности которые планируется сохранить в БД.
        foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof MoneyTransaction) {
                $this->processStatusChanging($entity);
            }
        }

        // Обрабатываем сущьности которые обновляются.
        foreach ($this->uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof MoneyTransaction) {
                $this->processStatusChanging($entity);
            }
        }
    }

    private function processStatusChanging(MoneyTransaction $transaction)
    {
        $changeSet = $this->uow->getEntityChangeSet($transaction);

        if (array_key_exists('status', $changeSet) && MoneyTransaction::STATUS_DONE === $changeSet['status'][1]) {
            // Получаем сущьности счетов для актуализации.
            $payee = $transaction->getDestination();
            $payer = $transaction->getSource();
            $amount = $transaction->getAmount();

            // Актуализируем балансы счетов.
            $payee->increaseBalance($amount)
                ->increaseProfit($amount);
            $payer->reduceBalance($amount);

            $metadata = $this->entityManager->getClassMetadata(Account::class);
            $this->uow->recomputeSingleEntityChangeSet($metadata, $payee);
            $this->uow->recomputeSingleEntityChangeSet($metadata, $payer);
        }
    }
}