<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CabinetController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Cabinet:index.html.twig');
    }

    public function profileAction()
    {
        return $this->render('AppBundle:Cabinet:profile.html.twig');
    }

    public function dashboardAction()
    {
        return $this->render('AppBundle:Cabinet:dashboard.html.twig');
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
