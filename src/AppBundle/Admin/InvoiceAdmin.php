<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Invoice;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class InvoiceAdmin extends Admin
{
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id', 'number', array(
                'label' => 'list.invoice.id'
            ))
            ->add('ticket.id', 'text', array(
                'label' => 'list.invoice.ticket'
            ))
            ->add('createdAt', 'datetime', array(
                'label' => 'list.invoice.created_at'
            ))
            ->add('updatedAt', 'datetime', array(
                'label' => 'list.invoice.updated_at'
            ))
            ->add('amount', 'number', array(
                'label' => 'list.invoice.amount'
            ))
            ->add('paid', 'number', array(
                'label' => 'list.invoice.paid'
            ))
            ->add('status', 'choice', array(
                'label' => 'list.invoice.status',
                'choices' => Invoice::getStatusLabels()
            ))
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list'));
    }
}