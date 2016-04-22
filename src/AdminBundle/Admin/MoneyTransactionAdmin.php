<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\MoneyTransaction;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class MoneyTransactionAdmin extends Admin
{
    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('source.number')
            ->add('destination.number')
            ->add('amount');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', 'number', array(
                'label' => 'list.transaction.id'
            ))
            ->add('source.number', 'text', array(
                'label' => 'list.transaction.payer'
            ))
            ->add('destination.number', 'text', array(
                'label' => 'list.transaction.payee'
            ))
            ->add('amount', 'currency', array(
                'label' => 'list.transaction.amount',
                'currency' => '$'
            ))
            ->add('type', 'choice', array(
                'label' => 'list.transaction.type',
                'choices' => MoneyTransaction::getTypeLabels(),
                'catalogue' => 'messages'
            ))
            ->add('status', 'choice', array(
                'label' => 'list.transaction.status',
                'choices' => MoneyTransaction::getStatusLabels(),
                'catalogue' => 'messages'
            ));
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id', 'number', array(
                'label' => 'show.transaction.id'
            ))
            ->add('source.number', 'text', array(
                'label' => 'show.transaction.payer'
            ))
            ->add('destination.number', 'text', array(
                'label' => 'show.transaction.payee'
            ))
            ->add('amount', 'currency', array(
                'label' => 'show.transaction.amount',
                'currency' => '$'
            ))
            ->add('type', 'choice', array(
                'label' => 'show.transaction.type',
                'choices' => MoneyTransaction::getTypeLabels(),
                'catalogue' => 'messages'
            ))
            ->add('status', 'choice', array(
                'label' => 'show.transaction.status',
                'choices' => MoneyTransaction::getStatusLabels(),
                'catalogue' => 'messages'
            ))
            ->add('note', 'text', array(
                'label' => 'show.transaction.note'
            ))
            ->add('external', 'number', array(
                'label' => 'show.transaction.external'
            ))
            ->add('invoice.id', 'number', array(
                'label' => 'show.transaction.invoice'
            ))
            ->add('attempts', 'number', array(
                'label' => 'show.transaction.attempts'
            ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'show'));
    }
}