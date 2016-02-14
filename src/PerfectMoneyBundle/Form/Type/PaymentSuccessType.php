<?php

namespace PerfectMoneyBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use PerfectMoneyBundle\Model\PaymentSuccess;

class PaymentSuccessType extends PaymentErrorType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('PAYER_ACCOUNT', HiddenType::class, array(
            'property_path' => 'payerAccount'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', PaymentSuccess::class);
    }
}