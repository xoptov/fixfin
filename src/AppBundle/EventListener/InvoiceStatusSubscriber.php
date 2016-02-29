<?php

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;

class InvoiceStatusSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush => 'onFlush'
        );
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $uow = $entityManager->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            if ($entity instanceof Invoice) {
                $user = $entity->getTicket()->getUser();

                if ($user->isCanInvite()) {
                    continue;
                }

                $changeSet = $uow->getEntityChangeSet($entity);

                if (!array_key_exists('status', $changeSet)) {
                    continue;
                }

                if (Invoice::STATUS_PAID === $changeSet['status'][1]) {
                    // Тут меняем значение статуса у пользователя чтобы он мог видеть свою реф ссылку
                    $user->setCanInvite(true);

                    $classMetadata = $entityManager->getClassMetadata(User::class);
                    $uow->recomputeSingleEntityChangeSet($classMetadata, $user);
                }
            }
        }
    }
}