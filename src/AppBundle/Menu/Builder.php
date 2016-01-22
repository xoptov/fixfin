<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->setChildrenAttribute('class', 'menu');

        $translator = $this->container->get('translator');

        $menu->addChild($translator->trans('menu.profile'), array(
            'route' => 'profile',
            'attributes' => array('class' => 'menu-item')
        ));

        $menu->addChild($translator->trans('menu.dashboard'), array(
            'route' => 'dashboard',
            'attributes' => array('class' => 'menu-item')
        ));
        $menu->addChild($translator->trans('menu.cloud'), array(
            'route' => 'cloud',
            'attributes' => array('class' => 'menu-item')
        ));
        $menu->addChild($translator->trans('menu.history'), array(
            'route' => 'history',
            'attributes' => array('class' => 'menu-item')
        ));
        $menu->addChild($translator->trans('menu.news'), array(
            'route' => 'news',
            'attributes' => array('class' => 'menu-item')
        ));
//        $menu->addChild($translator->trans('menu.content'), array(
//            'route' => 'content',
//            'attributes' => array('class' => 'menu-item')
//        ));
//        $menu->addChild($translator->trans('menu.faq'), array(
//            'route' => 'faq',
//            'attributes' => array('class' => 'menu-item')
//        ));

        return $menu;
    }
}