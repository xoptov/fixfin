<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\PhoneTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => 'form.user.username'
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'form.user.first_name'
            ))
            ->add('middleName', TextType::class, array(
                'label' => 'form.user.middle_name',
                'required' => false
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'form.user.last_name',
                'required' => false
            ))
            ->add('email', EmailType::class, array(
                'label' => 'form.user.email'
            ))
            ->add('phone', TextType::class, array(
                'label' => 'form.user.phone'
            ))
            ->add('account', PerfectMoneyAccountType::class, array(
                'label' => 'form.user.account'
            ))
            ->add('vkontakte', TextType::class, array(
                'label' => 'form.user.vkontakte',
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'form.user.save'
            ));

        $builder->get('phone')->addModelTransformer(new PhoneTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('Default', 'Profile')
        ));
    }
}