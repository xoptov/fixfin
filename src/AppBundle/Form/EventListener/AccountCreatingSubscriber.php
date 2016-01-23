<?php

namespace AppBundle\Form\EventListener;

use AppBundle\Entity\Account;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AccountCreatingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'onSubmit'
        );
    }

    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (null != $data && is_string($data)) {
            $account = new Account();
            $account->setNumber($data);
            $event->setData($account);
        }
    }
}