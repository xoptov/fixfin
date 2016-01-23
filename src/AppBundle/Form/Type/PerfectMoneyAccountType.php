<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\PerfectMoneyNumberToAccountTransformer;
use AppBundle\Form\EventListener\AccountCreatingSubscriber;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PerfectMoneyAccountType extends AbstractType
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PerfectMoneyNumberToAccountTransformer($this->objectManager);
        $builder->addViewTransformer($transformer);

        $subscriber = new AccountCreatingSubscriber();
        $builder->addEventSubscriber($subscriber);
    }

    public function getParent()
    {
        return TextType::class;
    }
}