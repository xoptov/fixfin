<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ProfileType;
use AppBundle\Form\Type\SupportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function profileAction(Request $request)
    {
        $form = $this->createForm(ProfileType::class, $this->getUser());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        $news = $this->getDoctrine()->getRepository('AppBundle:News')->getLastNews($this->getParameter('news_in_profile'));

        return $this->render('AppBundle:Default:profile.html.twig', array(
            'form' => $form->createView(),
            'news' => $news
        ));
    }

    public function cloudAction()
    {
        return $this->render('AppBundle:Default:cloud.html.twig');
    }

    public function historyAction()
    {
        $transactions = $this->getDoctrine()->getRepository('AppBundle:MoneyTransaction')
            ->getForUser($this->getUser());

        return $this->render('AppBundle:Default:history.html.twig', array('transactions' => $transactions));
    }

    public function newsAction()
    {
        $news = $this->getDoctrine()->getRepository('AppBundle:News')->findAll();

        return $this->render('AppBundle:Default:news.html.twig', array('news' => $news));
    }

    public function faqAction()
    {
        return $this->render('AppBundle:Default:faq.html.twig');
    }

    public function supportAction(Request $request)
    {
        $form = $this->createForm(SupportType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $issue = $form->getData();

            $entityManager->persist($issue);
            $entityManager->flush();

            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->add('success', 'Обращение в техподдрежку успешно принято!');
        }

        return $this->render('AppBundle:Default:support.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
