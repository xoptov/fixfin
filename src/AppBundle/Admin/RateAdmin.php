<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Entity\Pool;

class RateAdmin extends Admin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', 'text', array(
                'label' => 'form.rate.name'
            ))
            ->add('pool', 'entity', array(
                'label' => 'form.rate.pool',
                'class' => Pool::class,
                'property' => 'name'
            ))
            ->add('amount', 'number', array(
                'label' => 'form.rate.amount'
            ))
            ->add('commission', 'number', array(
                'label' => 'form.rate.commission'
            ))
            ->add('period', 'number', array(
                'label' => 'form.rate.period'
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('name')
            ->add('amount');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', 'number', array(
                'label' => 'list.rate.id'
            ))
            ->add('name', 'text', array(
                'label' => 'list.rate.name'
            ))
            ->add('pool.name', 'text', array(
                'label' => 'list.rate.pool'
            ))
            ->add('amount', 'number', array(
                'label' => 'list.rate.amount'
            ))
            ->add('commission', 'number', array(
                'label' => 'list.rate.commission'
            ))
            ->add('period', 'number', array(
                'label' => 'list.rate.period'
            ));
    }
}