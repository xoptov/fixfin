<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class APIController extends Controller
{
    public function accountsAction(Request $request)
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

        if ($account) {
            if ($account->getUser() == $this->getUser() || null === $account->getUser()) {
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
}
