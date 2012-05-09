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
        $menu->addChild(new MenuItem('Alkalmak', $router->generate('admin_term_index'), 1));
        $menu->addChild(new MenuItem('Oldalak', $router->generate('admin_textpage_index'), 2));
        $menu->addChild(new MenuItem('Megoldasok', $router->generate('admin_submission_index'), 3));
        $menu->addChild(new MenuItem('Felhasznalok', $router->generate('admin_user_index'), 10));
    }
}

