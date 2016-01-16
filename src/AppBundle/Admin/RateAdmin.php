<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RateAdmin extends Admin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form->add('name', 'text')
            ->add('pool', 'entity', array(
                'class' => 'AppBundle\\Entity\\Pool',
                'property' => 'name'
            ))
            ->add('amount', 'number')
            ->add('commission', 'number')
            ->add('period', 'number');
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid->add('name')
            ->add('amount');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('name', 'text')
            ->add('pool.name', 'text')
            ->add('amount', 'number')
            ->add('commission', 'percent')
            ->add('period', 'number');
    }
}