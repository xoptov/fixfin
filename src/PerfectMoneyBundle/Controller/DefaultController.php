<?php

namespace PerfectMoneyBundle\Controller;

use PerfectMoneyBundle\Event\PaymentEvent;
use PerfectMoneyBundle\Form\Type\PaymentConfirmationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function successAction()
    {
        //TODO: Тут необходимо сделать через форму PaymentSuccessType со всеми проверками потаму как мы имеем v2_hash в запросе.

        return new Response('Payment successful!');
    }

    /**
     * @return Response
     */
    public function failedAction()
    {
        //TODO: Тут необходимо сделать через форму PaymentErrorType.

        return new Response('Payment failed!', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function confirmationAction(Request $request)
    {
        $form = $this->createForm(PaymentConfirmationType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(PaymentEvent::CONFIRMED, new PaymentEvent($form->getData()));

            return new Response('Payment confirmed!', Response::HTTP_ACCEPTED);
        }

        $errors = $form->getErrors(true, false);

        return new Response((string)$errors, Response::HTTP_BAD_REQUEST);
    }
}
