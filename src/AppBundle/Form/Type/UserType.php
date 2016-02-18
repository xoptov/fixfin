<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\UsernameToUserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UsernameToUserTransformer($this->entityManager);

        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return TextType::class;
    }
}