<?php

namespace PerfectMoneyBundle\Controller;

use PerfectMoneyBundle\Event\PaymentEvent;
use PerfectMoneyBundle\Form\Type\PaymentConfirmationType;
use PerfectMoneyBundle\Form\Type\PaymentErrorType;
use PerfectMoneyBundle\Form\Type\PaymentSuccessType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function successAction(Request $request)
    {
        $form = $this->createForm(new PaymentSuccessType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(PaymentEvent::SUCCESS, new PaymentEvent($form->getData()));

            return new Response('Payment successful!'); //TODO: необходимо редиректить на страницу в кабинете
        }

        $errors = $form->getErrors(true, false);

        return new Response((string)$errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function failedAction(Request $request)
    {
        $form = $this->createForm(new PaymentErrorType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(PaymentEvent::FAILED, new PaymentEvent($form->getData()));

            return new Response('Payment failed!', Response::HTTP_BAD_REQUEST); //TODO: необходимо редиректить на страницу в кабинете
        }

        $errors = $form->getErrors(true, false);

        return new Response((string)$errors, Response::HTTP_BAD_REQUEST);
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

            return new Response('Payment confirmed!', Response::HTTP_ACCEPTED); //TODO: необходимо редиректить на страницу в кабинете
        }

        $errors = $form->getErrors(true, false);

        return new Response((string)$errors, Response::HTTP_BAD_REQUEST);
    }
}
