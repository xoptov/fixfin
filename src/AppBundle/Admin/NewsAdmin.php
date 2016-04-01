<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class NewsAdmin extends Admin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('title', 'text', array(
                'label' => 'form.news.title'
            ))
            ->add('content', 'textarea', array(
                'label' => 'form.news.content'
            ))
            ->add('enabled', 'choice', array(
                'choices' => array(
                    'status.common.no',
                    'status.common.yes'
                ),
                'label' => 'form.news.enabled'
            ));
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', 'number', array(
                'label' => 'list.news.id'
            ))
            ->add('title', 'text', array(
                'label' => 'list.news.subject'
            ))
            ->add('createdAt', 'datetime', array(
                'label' => 'list.news.created_at'
            ))
            ->add('updatedAt', 'datetime', array(
                'label' => 'list.news.updated_at'
            ))
            ->add('enabled', 'boolean', array(
                'label' => 'list.news.enabled'
            ));
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('title', 'text', array(
                'label' => 'show.news.subject'
            ))
            ->add('content', 'text', array(
                'label' => 'show.news.content'
            ))
            ->add('createdAt', 'datetime', array(
                'label' => 'show.news.created_at'
            ))
            ->add('updatedAt', 'datetime', array(
                'label' => 'show.news.updated_at'
            ))
            ->add('enabled', 'boolean', array(
                'label' => 'show.news.enabled'
            ));
    }
}