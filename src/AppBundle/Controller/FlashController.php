<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FlashController extends Controller
{
    public function messagesAction()
    {
        $flashBag = $this->get('session')->getFlashBag();
        $warnings = null;
        $success = null;

        if ($flashBag->has('warning')) {
            $warnings = $flashBag->get('warning');
        }

        if ($flashBag->has('success')) {
            $success = $flashBag->get('success');
        }

        return $this->render('AppBundle:Flash:messages.html.twig', array(
            'warnings' => $warnings,
            'success' => $success
        ));
    }
}