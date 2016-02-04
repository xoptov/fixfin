<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Rate;

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
     * @return Response
     */
    public function openAction(Rate $rate)
    {
        $user = $this->getUser();
        $ticket = $this->get('app.cashier_service')->openTable($user, $rate);

        return $this->render('AppBundle:Dashboard:open.html.twig', array('ticket' => $ticket));
    }

    public function prolongAction(Rate $rate)
    {
        return $this->render('AppBundle:Dashboard:index.html.twig');
    }

}
