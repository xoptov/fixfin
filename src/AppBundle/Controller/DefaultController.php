<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ProfileType;
use AppBundle\Form\Type\SupportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    public function profileAction(Request $request)
    {
        $form = $this->createForm(ProfileType::class, $this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->flush();
            $bag = $this->get('session')->getFlashBag();
            $bag->add('success', 'Профиль успешно изменён!');
        }

        $news = $entityManager->getRepository('AppBundle:News')
            ->getLastNews($this->getParameter('news_in_profile'));

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

    public function notificationsAction()
    {
        $limit = $this->getParameter('notify_show_limit');

        $notifications = $this->getDoctrine()->getRepository('AppBundle:Notification')
            ->getLastUnread($this->getUser(), $limit);

        return $this->render('AppBundle:Default:notifications.html.twig', array(
            'notifications' => $notifications
        ));
    }
}
