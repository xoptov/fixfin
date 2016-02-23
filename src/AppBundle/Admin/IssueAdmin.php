<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class IssueAdmin extends Admin
{
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', 'number', array(
                'label' => 'list.issue.id'
            ))
            ->add('subject', 'text', array(
                'label' => 'list.issue.subject'
            ))
            ->add('email', 'text', array(
                'label' => 'list.issue.email'
            ))
            ->add('createdAt', 'datetime', array(
                'label' => 'list.issue.created_at'
            ));
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('subject', 'text', array(
                'label' => 'show.issue.subject'
            ))
            ->add('body', 'text', array(
                'label' => 'show.issue.body'
            ))
            ->add('email', 'text', array(
                'label' => 'show.issue.email'
            ))
            ->add('createdAt', 'datetime', array(
                'label' => 'show.issue.created_at'
            ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list', 'show'));
    }
}