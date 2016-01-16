<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LandingController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Landing:index.html.twig');
    }
}
