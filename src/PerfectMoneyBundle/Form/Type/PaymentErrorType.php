<?php

namespace PerfectMoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PaymentErrorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('PAYEE_ACCOUNT', HiddenType::class, array(
                'property_path' => 'payeeAccount'
            ))
            ->add('PAYMENT_AMOUNT', HiddenType::class, array(
                'property_path' => 'paymentAmount'
            ))
            ->add('PAYMENT_UNITS', HiddenType::class, array(
                'property_path' => 'paymentUnits'
            ))
            ->add('PAYMENT_BATCH_NUM', HiddenType::class, array(
                'property_path' => 'paymentBatchNum'
            ))
            ->add('PAYMENT_ID', HiddenType::class, array(
                'property_path' => 'paymentId'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PerfectMoneyBundle\\Model\\PaymentError',
            'csrf_protection' => false
        ));
    }
}