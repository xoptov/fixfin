<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityManagerInterface;

class PoolAdmin extends Admin
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', 'text', array(
                'label' => 'form.pool.name'
            ))
            ->add('accounts', 'sonata_type_model', array(
                'label' => 'form.pool.accounts',
                'choices' => null,
                'property' => 'number',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'btn_add' => false,
                'query' => $this->getQueryBuilder()
            ));
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('name')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array()
                )
            ));
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id', 'number')
            ->add('name', 'text', array(
                'label' => 'show.pool.name'
            ))
            ->add('accounts', 'sonata_type_model', array(
                'label' => 'show.pool.accounts',
                'associated_property' => 'number'
            ));
    }

    protected function getQueryBuilder()
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Account', 'a')->where('a.system = true');

        return $qb;
    }
}