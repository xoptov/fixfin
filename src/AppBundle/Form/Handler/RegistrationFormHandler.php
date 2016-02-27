<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\Notification;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseRegistrationFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Model\UserInterface;
use AppBundle\Entity\User;
use Sonata\NotificationBundle\Backend\BackendInterface;

class RegistrationFormHandler extends BaseRegistrationFormHandler
{
    /** @var BackendInterface */
    private $notificationBackend;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, BackendInterface $notificationBackend)
    {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);

        $this->notificationBackend = $notificationBackend;
    }

    /**
     * @param UserInterface $user
     * @param bool $confirmation
     */
    public function onSuccess(UserInterface $user, $confirmation)
    {
        $referrer = null;
        $session = $this->request->getSession();

        /** @var User $user */
        if ($user->getReferrer()) {
            $referrer = $user->getReferrer();
        } elseif ($session->has('referrer')) {
            /** @var User $referrer */
            $referrer = $this->userManager->findUserByUsernameOrEmail($session->get('referrer'));
            if ($referrer instanceof UserInterface) {
                /** @var User $user */
                $user->setReferrer($referrer);
            }
        }

        parent::onSuccess($user, $confirmation);

        if ($referrer instanceof User) {
            // Нотификация для лидера о том что зарегистрирован новый реферал
            $this->notificationBackend->createAndPublish('notification', array(
                'userId' => $referrer->getId(),
                'type' => Notification::TYPE_NEW_REFERRAL,
                'content' => 'В структуру добавлен новый реферал.'
            ));
        }
    }
}