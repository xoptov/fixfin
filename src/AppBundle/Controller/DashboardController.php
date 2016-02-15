<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Rate;
use PerfectMoneyBundle\Form\Type\PaymentRequestType;
use AppBundle\Entity\Ticket;

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
     * @param Rate $rate
     * @return Response
     */
    public function openAction(Rate $rate)
    {
        $user = $this->getUser();
        $this->get('app.cashier_service')->openTable($user, $rate);

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @param Ticket $ticket
     * @return RedirectResponse|Response
     */
    public function prolongAction(Ticket $ticket)
    {
        //TODO: нужно подумать где необходимо делать валидацию, или упаковать это в сервис одного метода
        $paymentRequest = $this->get('app.cashier_service')->createProlongPaymentRequest($ticket);

        $pmOptions = $this->getParameter('perfect_money');
        $options = array('action' => $this->get('property_accessor')->getValue($pmOptions, '[entry_form]'));

        $form = $this->createForm(PaymentRequestType::class, $paymentRequest, $options);

        return $this->render('AppBundle:Dashboard:prolong.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Rate $rate
     * @return Response
     */
    public function tableAction(Rate $rate)
    {
        $ticket = $this->getDoctrine()->getRepository('AppBundle:Ticket')
            ->getTicketByRate($rate, $this->getUser());

        return $this->render('AppBundle:Dashboard:table.html.twig', array(
            'rate' => $rate,
            'ticket' => $ticket
        ));
    }
}
