<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Ticket;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class TicketAdmin extends Admin
{
    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', 'number', array(
                'label' => 'list.ticket.id'
            ))
            ->add('chiefTicket.id', 'text', array(
                'label' => 'list.ticket.chief_ticket'
            ))
            ->add('rate.name', 'text', array(
                'label' => 'list.ticket.rate'
            ))
            ->add('user.username', 'text', array(
                'label' => 'list.ticket.owner'
            ))
            ->add('paidUp', 'datetime', array(
                'label' => 'list.ticket.paid_up'
            ))
            ->add('type', 'choice', array(
                'label' => 'list.ticket.type',
                'choices' => Ticket::getTypeLabels()
            ))
            ->add('_action', 'actions', array(
                'label' => 'list.ticket.actions',
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('chiefTicket', 'sonata_type_model', array(
                'label' => 'form.ticket.chief_ticket',
                'property' => 'id',
                'choices' => null,
                'btn_add' => false,
                'required' => false
            ))
            ->add('rate', 'sonata_type_model', array(
                'label' => 'form.ticket.rate',
                'property' => 'name',
                'choices' => null,
                'btn_add' => false
            ))
            ->add('user', 'sonata_type_model', array(
                'label' => 'form.ticket.owner',
                'property' => 'username',
                'choices' => null,
                'btn_add' => false
            ))
            ->add('paidUp', 'datetime', array(
                'label' => 'form.ticket.paid_up',
                'required' => false
            ))
            ->add('type', 'choice', array(
                'label' => 'form.ticket.type',
                'choices' => Ticket::getTypeLabels()
            ));
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id', 'number', array(
                'label' => 'show.ticket.id'
            ))
            ->add('chiefTicket.id', 'text', array(
                'label' => 'show.ticket.chief_ticket'
            ))
            ->add('rate.name', 'text', array(
                'label' => 'show.ticket.rate'
            ))
            ->add('user.username', 'text', array(
                'label' => 'show.ticket.owner'
            ))
            ->add('paidUp', 'datetime', array(
                'label' => 'show.ticket.paid_up'
            ))
            ->add('type', 'choice', array(
                'label' => 'show.ticket.type',
                'choices' => Ticket::getTypeLabels()
            ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}
