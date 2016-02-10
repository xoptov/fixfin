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
        $form = $this->createForm(ProfileType::class, $this->getUser());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        $news = $this->getDoctrine()->getRepository('AppBundle:News')->getLastNews($this->getParameter('news_in_profile'));
        
        return $this->render('AppBundle:Cabinet:profile.html.twig', array(
            'form' => $form->createView(),
            'news' => $news
        ));
    }

    public function cloudAction()
    {
        return $this->render('AppBundle:Cabinet:cloud.html.twig');
    }

    public function historyAction()
    {
        $transactions = $this->getDoctrine()->getRepository('AppBundle:MoneyTransaction')
            ->getForUser($this->getUser());

        return $this->render('AppBundle:Cabinet:history.html.twig', array('transactions' => $transactions));
    }

    public function contentAction()
    {
        return $this->render('AppBundle:Cabinet:content.html.twig');
    }

    public function newsAction()
    {
        $news = $this->getDoctrine()->getRepository('AppBundle:News')->findAll();

        return $this->render('AppBundle:Cabinet:news.html.twig', array('news' => $news));
    }

    public function faqAction()
    {
        return $this->render('AppBundle:Cabinet:faq.html.twig');
    }

}
