<?php

namespace PerfectMoneyBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PerfectMoneyBundle\Service\TokenProviderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class V2HashValidationSubscriber implements EventSubscriberInterface
{
    /** @var TokenProviderInterface */
    private $provider;

    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(TokenProviderInterface $provider, PropertyAccessor $accessor)
    {
        $this->provider = $provider;
        $this->accessor = $accessor;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    /**
     * @param FormEvent $event
     * @return bool
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($form->isRoot()) {
            $data['PASS_PHRASE_HASH'] = $this->provider->getToken($this->accessor->getValue($data, '[PAYEE_ACCOUNT]'));

            $fields = array('PAYMENT_ID', 'PAYEE_ACCOUNT', 'PAYMENT_AMOUNT', 'PAYMENT_UNITS', 'PAYMENT_BATCH_NUM', 'PAYER_ACCOUNT', 'PASS_PHRASE_HASH', 'TIMESTAMPGMT');
            $tokenBody = array();

            // Подготовка данных для вычесления хэша
            foreach ($fields as $field) {
                $value = $this->accessor->getValue($data, '['.$field.']');
                $tokenBody[] = $value ? $value : 'NULL';
            }

            // Собираем строку для хэширования
            $tokenBody = strtoupper(implode(':', $tokenBody));
            $expectedHash = strtoupper(md5($tokenBody));

            if ($this->accessor->getValue($data, '[V2_HASH]') !== $expectedHash) {
                $form->addError(new FormError('Checksum mismatch!'));

                return false;
            }
        } else {
            $form->addError(new FormError('V2_HASH checking available only for form!'));

            return false;
        }

        return true;
    }
}