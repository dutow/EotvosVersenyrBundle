<?php

namespace Eotvos\VersenyrBundle\Cwm;

use Cancellar\CommonBundle\Cwm\AdminModuleInterface;
use Cancellar\CommonBundle\Menu\Menu;
use Cancellar\CommonBundle\Menu\MenuItem;

class VersenyrAdminModule implements AdminModuleInterface
{

    public function constructMenu(Menu $menu, $container)
    {
        $router = $container->get('router');
        $menu->addChild(new MenuItem('admin.heading.term', $router->generate('admin_term_index'), 1));
        $menu->addChild(new MenuItem('admin.heading.textpage', $router->generate('admin_textpage_index'), 2));
        $menu->addChild(new MenuItem('admin.heading.submission', $router->generate('admin_submission_index'), 3));
        $menu->addChild(new MenuItem('admin.heading.configuration', $router->generate('admin_configuration_index'), 4));
        $menu->addChild(new MenuItem('admin.heading.user', $router->generate('admin_user_index'), 10));
    }
}

