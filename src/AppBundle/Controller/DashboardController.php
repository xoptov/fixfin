<?php

namespace AppBundle\Controller;

use AppBundle\Event\TicketEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Rate;
use PerfectMoneyBundle\Form\Type\PaymentRequestType;
use AppBundle\Entity\Ticket;
use Symfony\Component\Validator\ConstraintViolationInterface;

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
    public function paymentTableAction(Rate $rate)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getAccount()) {
            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->add('warning', 'Для оплаты стола необходимо указать счёт Perfect Money в профиле!');

            return $this->redirectToRoute('app_profile');
        }

        // Получаем новый или уже имеющийся инвойс на оплату.
        $invoice = $this->get('app.cashier_service')->getInvoiceForPayment($user, $rate);

        // Сохраняем сейчас инвойс в БД для получения актуального ID иначе нихуя неработает.
        $this->getDoctrine()->getManager()->flush();

        // Создаем запрос на оплату.
        $paymentRequest = $this->get('app.banker_service')->createPaymentRequest($invoice);

        //TODO: Необходимо реализовать валидацию на созданный запрос.

        $pmOptions = $this->getParameter('perfect_money');
        $options = array('action' => $this->get('property_accessor')->getValue($pmOptions, '[entry_form]'));

        // Генерируем форму для отправки в Perfect Money.
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
