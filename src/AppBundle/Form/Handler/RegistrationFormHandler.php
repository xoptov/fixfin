<?php

namespace AppBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseRegistrationFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Model\UserInterface;
use AppBundle\Entity\User;

class RegistrationFormHandler extends BaseRegistrationFormHandler
{
    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
    }

    public function onSuccess(UserInterface $user, $confirmation)
    {
        $session = $this->request->getSession();

        if ($session->has('referrer')) {
            $referrer = $this->userManager->findUserByUsernameOrEmail($session->get('referrer'));
            if ($referrer instanceof UserInterface) {
                /** @var User $user */
                $user->setReferrer($referrer);
            }
        }

        parent::onSuccess($user, $confirmation);
    }
}