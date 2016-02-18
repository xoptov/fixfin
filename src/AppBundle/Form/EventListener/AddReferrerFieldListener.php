<?php

namespace AppBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Form\Type\UserType;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddReferrerFieldListener
{
    /** @var SessionInterface */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param FormEvent $event
     */
    public function __invoke(FormEvent $event)
    {
        $form = $event->getForm();

        if (!$this->session->has('referrer')) {
            $form->add('referrer', UserType::class, array(
                'constraints' => new NotBlank(),
            ));
        }
    }
}