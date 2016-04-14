<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\PerfectMoneyNumberToAccountTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PerfectMoneyAccountType extends AbstractType
{
    private $objectManager;

    private $logger;

    public function __construct(ObjectManager $objectManager, LoggerInterface $logger)
    {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PerfectMoneyNumberToAccountTransformer($this->objectManager, $this->logger);
        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return TextType::class;
    }
}