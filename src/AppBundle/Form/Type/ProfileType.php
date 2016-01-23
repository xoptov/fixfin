<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, array(
                'label' => 'form.user.first_name'
            ))
            ->add('middleName', TextType::class, array(
                'label' => 'form.user.middle_name'
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'form.user.last_name'
            ))
            ->add('email', EmailType::class, array(
                'label' => 'form.user.email'
            ))
            ->add('phone', TextType::class, array(
                'label' => 'form.user.phone'
            ))
            ->add('account', PerfectMoneyAccountType::class)
            ->add('save', SubmitType::class, array(
                'label' => 'form.user.save'
            ));

        $builder->get('phone')->addModelTransformer(new PhoneTransformer());
    }
}