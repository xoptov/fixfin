<?php

namespace PerfectMoneyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaymentRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('PAYEE_ACCOUNT', HiddenType::class, array(
                'property_path' => 'payeeAccount.number'
            ))
            ->add('PAYEE_NAME', HiddenType::class, array(
                'property_path' => 'payeeName'
            ))
            ->add('PAYMENT_AMOUNT', HiddenType::class, array(
                'property_path' => 'paymentAmount'
            ))
            ->add('PAYMENT_UNITS', HiddenType::class, array(
                'property_path' => 'paymentUnits'
            ))
            ->add('PAYMENT_ID', HiddenType::class, array(
                'property_path' => 'paymentId'
            ))
            ->add('STATUS_URL', HiddenType::class, array(
                'property_path' => 'statusUrl'
            ))
            ->add('PAYMENT_URL', HiddenType::class, array(
                'property_path' => 'paymentUrl'
            ))
            ->add('NOPAYMENT_URL', HiddenType::class, array(
                'property_path' => 'noPaymentUrl'
            ))
            ->add('AVAILABLE_PAYMENT_METHODS', HiddenType::class, array(
                'property_path' => 'availablePaymentMethods'
            ))
            ->add('paying', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PerfectMoneyBundle\\Model\\PaymentRequest',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}