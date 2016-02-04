<?php

namespace PerfectMoneyBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ConfirmationType extends PaymentSuccessType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('TIMESTAMPGMT', HiddenType::class, array(
                'property_path' => 'timestampGmt'
            ))
            ->add('V2_HASH', HiddenType::class, array(
                'property_path' => 'v2Hash'
            ));

        //todo необходимо втулить subscriber для проверки валидности v2_hash пример можно взять в CsrfType
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', 'PerfectMoneyBundle\\Model\\PaymentConfirmation');
    }
}