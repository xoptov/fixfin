<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends Admin
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('username', 'text', array(
                'label' => 'form.user.username'
            ))
            ->add('plainPassword', 'password', array(
                'label' => 'form.user.password',
                'required' => false
            ))
            ->add('firstName', 'text', array(
                'label' => 'form.user.first_name',
                'required' => false
            ))
            ->add('middleName', 'text', array(
                'label' => 'form.user.middle_name',
                'required' => false
            ))
            ->add('lastName', 'text', array(
                'label' => 'form.user.last_name',
                'required' => false
            ))
            ->add('email', 'text', array(
                'label' => 'form.user.email'
            ))
            ->add('phone', 'text', array(
                'label' => 'form.user.phone',
                'required' => false
            ))
            ->add('account', 'sonata_type_model', array(
                'label' => 'form.user.account',
                'property' => 'number',
                'required' => false,
                'choices' => null,
                'query' => $this->getQueryBuilder()
            ))
            ->add('referrer', 'entity', array(
                'label' => 'form.user.referrer',
                'class' => 'AppBundle\\Entity\\User',
                'property' => 'username',
                'required' => false
            ))
            ->add('locked', 'choice', array(
                'label' => 'form.user.locked',
                'choices' => User::getLockedLabels()
            ))
            ->add('enabled', 'choice', array(
                'label' => 'form.user.enabled',
                'choices' => User::getEnabledLabels()
            ));
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('username', 'text', array(
                'label' => 'list.user.username'
            ))
            ->add('account.number', 'text', array(
                'label' => 'list.user.account'
            ))
            ->add('phone', 'text', array(
                'label' => 'list.user.phone'
            ))
            ->add('referrer.username', 'text', array(
                'label' => 'list.user.referrer'
            ))
            ->add('locked', 'boolean', array(
                'label' => 'list.user.locked'
            ))
            ->add('enabled', 'boolean', array(
                'label' => 'list.user.enabled'
            ))
            ->add('_action', 'actions', array(
                'label' => 'list.user.actions',
                'actions' => array(
                    'show' => array(),
                    'edit' => array()
                )
            ));
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id', 'number')
            ->add('username', 'text', array(
                'label' => 'show.user.username'
            ))
            ->add('firstName', 'text', array(
                'label' => 'show.user.first_name'
            ))
            ->add('middleName', 'text', array(
                'label' => 'show.user.middle_name'
            ))
            ->add('lastName', 'text', array(
                'label' => 'show.user.last_name'
            ))
            ->add('email', 'text', array(
                'label' => 'show.user.email'
            ))
            ->add('phone', 'text', array(
                'label' => 'show.user.phone'
            ))
            ->add('account.number', 'text', array(
                'label' => 'show.user.account'
            ))
            ->add('referrer.username', 'text', array(
                'label' => 'show.user.referrer'
            ))
            ->add('locked', 'boolean', array(
                'label' => 'show.user.locked'
            ))
            ->add('enabled', 'boolean', array(
                'label' => 'show.user.enabled'
            ));
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    public function prePersist($object)
    {
        /** @var Account $account */
        if ($account = $object->getAccount()) {
            $account->setUser($object);
        }

        $this->userManager->updateUser($object, false);
    }

    public function preUpdate($object)
    {
        /** @var Account $account */
        if ($account = $object->getAccount()) {
            $account->setUser($object);
        }

        $this->userManager->updateUser($object, false);
    }

    protected function getQueryBuilder()
    {
        $user = $this->getSubject();

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Account', 'a');

        if ($user->getId()) {
            $qb->where('(a.user IS NULL OR a.user = :user) AND a.system = false')->setParameter('user', $user);
        } else {
            $qb->where('a.user IS NULL AND a.system = false');
        }

        return $qb;
    }
}