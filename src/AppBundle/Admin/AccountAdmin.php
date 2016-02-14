<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity\User;

class AccountAdmin extends Admin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form->add('user', 'entity', array(
            'label' => 'form.account.user',
            'required' => false,
            'class' => User::class,
            'property' => 'username')
        )
            ->add('number', 'text', array(
                'label' => 'form.account.number'
            ))
            ->add('login', 'text', array(
                'label' => 'form.account.login',
                'required' => false
            ))
            ->add('password', 'password', array(
                'label' => 'form.account.password',
                'required' => false
            ))
            ->add('passPhrase', 'password', array(
                'required' => false,
                'label' => 'form.account.pass_phrase'
            ))
            ->add('commission', 'number', array(
                'required' => false,
                'label' => 'form.account.commission'
            ))
            ->add('system', 'choice', array(
                'choices' => array(
                    'status.common.no',
                    'status.common.yes'
                ),
                'label' => 'form.account.system'
            ))
            ->add('verified', 'choice', array(
                'choices' => array(
                    'status.common.no',
                    'status.common.yes'
                ),
                'label' => 'form.account.verified'
            ))
            ->add('blocked', 'choice', array(
                'choices' => array(
                    'status.common.no',
                    'status.common.yes'
                ),
                'label' => 'form.account.blocked'
            ))
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id', 'number')
            ->add('number', 'text', array(
                'label' => 'list.account.number'
            ))
            ->add('user.username', 'text', array(
                'label' => 'list.account.owner'
            ))
            ->add('commission', 'number', array(
                'label' => 'list.account.commission'
            ))
            ->add('system', 'boolean', array(
                'label' => 'list.account.system'
            ))
            ->add('balance', 'number', array(
                'label' => 'list.account.balance'
            ))
            ->add('verified', 'boolean', array(
                'label' => 'list.account.verified'
            ))
            ->add('blocked', 'boolean', array(
                'label' => 'list.account.blocked'
            ))
            ->add('_action', 'actions', array(
                'label' => 'list.account.actions',
                'actions' => array(
                    'show' => array(),
                    'edit' => array()
                )
            ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}