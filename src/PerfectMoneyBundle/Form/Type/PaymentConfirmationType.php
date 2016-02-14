<?php

namespace PerfectMoneyBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use PerfectMoneyBundle\Service\TokenProviderInterface;
use PerfectMoneyBundle\Form\EventListener\V2HashValidationSubscriber;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use PerfectMoneyBundle\Model\PaymentConfirmation;

class PaymentConfirmationType extends PaymentSuccessType
{
    /** @var TokenProviderInterface */
    private $tokenProvider;

    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(TokenProviderInterface $tokenProvider, PropertyAccessor $accessor)
    {
        $this->tokenProvider = $tokenProvider;
        $this->accessor = $accessor;
    }

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

        $v2HashSubscriber = new V2HashValidationSubscriber($this->tokenProvider, $this->accessor);
        $builder->addEventSubscriber($v2HashSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', PaymentConfirmation::class);
    }

    public function getName()
    {
        return '';
    }
}