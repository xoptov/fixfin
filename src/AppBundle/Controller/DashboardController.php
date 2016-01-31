<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;

class DashboardController extends Controller
{
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $rates = $this->getDoctrine()->getRepository('AppBundle:Rate')->findAll();

        return $this->render('AppBundle:Dashboard:index.html.twig', array(
            'rates' => $rates,
            'tickets' => $user->getTickets()
        ));
    }

    /**
     * 1. Необходимо проверить есть ли у referrer подходящий тикет
     * 2. Необходимо сделать поиск по ветке у кого есть подходящий тикет
     * 3. Необходимо привязаться к найденному тикету
     */
    public function joinAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');


        return $this->render('AppBundle:Dashboard:join.html.twig');
    }

    public function prolongAction()
    {
        return $this->render('AppBundle:Dashboard:index.html.twig');
    }

}
