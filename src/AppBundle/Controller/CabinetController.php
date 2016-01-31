<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CabinetController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Cabinet:index.html.twig');
    }

    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new ProfileType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('AppBundle:Cabinet:profile.html.twig', array('form' => $form->createView()));
    }

    public function cloudAction()
    {
        return $this->render('AppBundle:Cabinet:cloud.html.twig');
    }

    public function historyAction()
    {
        return $this->render('AppBundle:Cabinet:history.html.twig');
    }

    public function contentAction()
    {
        return $this->render('AppBundle:Cabinet:content.html.twig');
    }

    public function newsAction()
    {
        return $this->render('AppBundle:Cabinet:news.html.twig');
    }

    public function faqAction()
    {
        return $this->render('AppBundle:Cabinet:faq.html.twig');
    }

}
