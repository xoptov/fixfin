<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class UserAdmin extends Admin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form->add('username')
            ->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->add('phone', 'text')
            ->add('account', 'entity', array(
                'class' => 'AppBundle\\Entity\\Account',
                'property' => 'number'
            ))
            ->add('referrer', 'entity', array(
                'class' => 'AppBundle\\Entity\\User',
                'property' => 'username'
            ))
            ->add('avatar', 'text');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('username')
            ->add('firstName')
            ->add('middleName')
            ->add('lastName')
            ->add('phone')
            ->add('account.number')
            ->add('referrer.username')
            ->add('avatar');
    }
}