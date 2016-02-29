<?php

namespace AppBundle\Notification\Consumer;

use AppBundle\Entity\Notification;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Entity\User;

class NotificationConsumer implements ConsumerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(EntityManagerInterface $entityManager, PropertyAccessor $accessor)
    {
        $this->entityManager = $entityManager;
        $this->accessor = $accessor;
    }

    public function process(ConsumerEvent $event)
    {
        $message = $event->getMessage();

        $userId = $this->accessor->getValue($message, 'body[userId]');
        $type = $this->accessor->getValue($message, 'body[type]');
        $content = $this->accessor->getValue($message, 'body[content]');

        $user = $this->entityManager->getReference(User::class, $userId);

        $notification = new Notification();
        $notification->setUser($user)
            ->setType($type)
            ->setContent($content);

        $this->entityManager->persist($notification);

        $this->entityManager->flush();
    }
}