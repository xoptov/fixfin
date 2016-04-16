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

    /** @var string */
    private $defaultUsername;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, BackendInterface $notificationBackend)
    {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);

        $this->notificationBackend = $notificationBackend;
    }

    /**
     * @param $username
     */
    public function setDefaultUsername($username)
    {
        $this->defaultUsername = $username;
    }

    /**
     * @param UserInterface $user
     * @param bool $confirmation
     */
    public function onSuccess(UserInterface $user, $confirmation)
    {
        $session = $this->request->getSession();

        // Пробуем назначить лидера из сессии.
        /** @var User $user */
        if (!$user->getReferrer() && $session->has('referrer')) {
            /** @var User $referrer */
            $this->tryDetermineReferrer($user, $session->get('referrer'));
        }

        // Если лидер из сессии неназначен то пробуем назначить лидера по умолчанию.
        if (!$user->getReferrer()) {
            $this->tryDetermineReferrer($user, $this->defaultUsername);
        }

        parent::onSuccess($user, $confirmation);

        if ($user->getReferrer() instanceof User) {
            // Нотификация для лидера о том что зарегистрирован новый реферал
            $this->notificationBackend->createAndPublish('notification', array(
                'userId' => $user->getReferrer()->getId(),
                'type' => Notification::TYPE_NEW_REFERRAL,
                'content' => 'В структуру добавлен новый реферал.'
            ));
        }
    }

    public function tryDetermineReferrer(User $user, $username) {
        /** @var User $referrer */
        $referrer = $this->userManager->findUserByUsernameOrEmail($username);

        if ($referrer) {
            /** @var User $user */
            $user->setReferrer($referrer);
        }
    }
}