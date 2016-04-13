<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class APIController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postAccountAction(Request $request)
    {
        $newAccount = new Account();
        $newAccount->setNumber($request->request->get('number'));

        $validator = $this->get('validator');
        $violations = $validator->validate($newAccount);

        if ($violations->count()) {
            $error = $violations->get(0)->getMessage();
            return new JsonResponse(['error' => $error], Response::HTTP_BAD_REQUEST);
        }

        $account = $this->getDoctrine()->getRepository('AppBundle:Account')
            ->findOneBy(array('number' => $newAccount->getNumber()));

        if ($account instanceof Account) {
            if ($account->getUser() === $this->getUser() || null === $account->getUser()) {
                return new JsonResponse(['id' => $account->getId()]);
            } else {
                return new JsonResponse(['error' => 'Счет испольуется другим пользователем.'], Response::HTTP_BAD_REQUEST);
            }
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($newAccount);
        $manager->flush();

        return new JsonResponse(['id' => $newAccount->getId()]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postAvatarAction(Request $request)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('avatar');

        if (!$uploadedFile) {
            return new JsonResponse(['errors' => 'Неудалось загрузить аватарку']);
        }

        $errors = $this->get('validator')->validate($uploadedFile);

        if ($errors->count()) {
            return new JsonResponse(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        $filename = md5($uploadedFile->getSize()) . '.png';
        $uploadedFile->move($this->getParameter('uploads_path'), $filename);
        $uploadedUri = $this->getParameter('uploads_dir') . DIRECTORY_SEPARATOR . $filename;

        /** @var User $user */
        $user = $this->getUser();
        $user->setAvatar($uploadedUri);
        $this->get('fos_user.user_manager')->updateUser($user);

        $webPath = $this->get('liip_imagine.cache.manager')->getBrowserPath($uploadedUri, 'avatar_125x125');

        return new JsonResponse(['path' => $webPath]);
    }

    /**
     * @return JsonResponse
     */
    public function getNotificationsAction()
    {
        $user = $this->getUser();
        $limit = $this->getParameter('notify_show_limit');

        $notifications = $this->getDoctrine()->getRepository('AppBundle:Notification')
            ->getLastUnread($user, $limit);

        $results = array();

        /** @var Notification $notification */
        foreach ($notifications as $notification){
            $results[] = array(
                'id' => $notification->getId(),
                'created' => $notification->getCreatedAt()->format('d.m.Y'),
                'content' => $notification->getContent()
            );
        }

        return new JsonResponse([
            'count' => count($results),
            'items' => $results
        ]);
    }

    /**
     * @param Notification $notification
     * @return JsonResponse
     */
    public function getNotificationAction(Notification $notification)
    {
        //TODO: Необходимо помечать нотификацию как прочитанную и возвращать эту нотификацию обратно.
        $notification->setViewed(true);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'id' => $notification->getId(),
            'content' => $notification->getContent(),
            'viewed' => $notification->isViewed()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function searchReferrersAction(Request $request)
    {
        $query = $request->get('query');

        $users = $this->getDoctrine()->getRepository('AppBundle:User')
            ->searchReferrers($query);

        $json = $this->get('serializer')->serialize(['data' => $users], 'json');

        return new Response($json, Response::HTTP_OK, array('Content-Type' => 'application/json'));
    }
}
