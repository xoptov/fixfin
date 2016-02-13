<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class MoneyTransactionAdmin extends Admin
{
    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid->add('source.number')
            ->add('destination.number')
            ->add('amount');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('source.number')
            ->add('destination.number')
            ->add('amount');
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id', 'number', array(
            'label' => 'show.transaction.id'
        ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'show'));
    }
}