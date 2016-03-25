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
            'route' => 'app_profile',
            'attributes' => array('class' => 'menu-item')
        ));

        $menu->addChild($translator->trans('menu.dashboard'), array(
            'route' => 'app_dashboard',
            'attributes' => array('class' => 'menu-item')
        ));
        $menu->addChild($translator->trans('menu.cloud'), array(
            'route' => 'app_cloud',
            'attributes' => array('class' => 'menu-item')
        ));
        $menu->addChild($translator->trans('menu.history'), array(
            'route' => 'app_history',
            'attributes' => array('class' => 'menu-item')
        ));
        $menu->addChild($translator->trans('menu.news'), array(
            'route' => 'app_news',
            'attributes' => array('class' => 'menu-item')
        ));

        return $menu;
    }
}