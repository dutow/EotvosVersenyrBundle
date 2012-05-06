<?php

namespace Eotvos\VersenyrBundle\Cwm;

use Cancellar\CommonBundle\Cwm\ModuleInterface;

class VersenyrModule implements ModuleInterface
{

    public function getDependencies()
    {
        return array();
    }

    public function getAdminModule()
    {
        return new VersenyrAdminModule();
    }
}

