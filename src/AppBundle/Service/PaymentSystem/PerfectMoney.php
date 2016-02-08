<?php

namespace AppBundle\Service\PaymentSystem;

use PerfectMoneyBundle\Model\PaymentRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Pool;

class PerfectMoney
{
    /** @var PropertyAccessor */
    private $accessor;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $options;

    public function __construct(PropertyAccessor $accessor, EntityManagerInterface $entityManager, array $options)
    {
        $this->accessor = $accessor;
        $this->entityManager = $entityManager;

        // Настройка опций для сервиса
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function createPaymentRequest(Invoice $invoice)
    {
        $request = new PaymentRequest();
        $request->setPaymentId($invoice->getId())
            ->setPaymentAmount($invoice->getAmount())
            ->setPayeeName($this->options['payee_name'])
            ->setPaymentUnits($this->options['payment_units'])
            ->setStatusUrl($this->options['status_url'])
            ->setPaymentUrl($this->options['payment_url'])
            ->setNoPaymentUrl($this->options['nopayment_url'])
            ->setAvailablePaymentMethods($this->options['available_payment_methods']);

        $pool = $this->accessor->getValue($invoice, 'ticket.rate.pool');

        if (!$pool instanceof Pool) {
            throw new AccessException();
        }

        $account = $this->entityManager->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($pool);

        $request->setPayeeAccount($account);

        return $request;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'payee_name' => 'FixFin ltd.',
            'payment_units' => 'USD'
        ));

        $resolver->setRequired(array('status_url', 'payment_url', 'nopayment_url', 'available_payment_methods'));
    }
}