<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\EventListener\AddReferrerFieldListener;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RegistrationType extends RegistrationFormType
{
    /** @var SessionInterface */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, new AddReferrerFieldListener($this->session));
    }

    public function getName()
    {
        return 'form_user_registration';
    }
}