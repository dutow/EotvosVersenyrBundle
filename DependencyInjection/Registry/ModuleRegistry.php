<?php
namespace Eotvos\VersenyrBundle\DependencyInjection\Registry;

class ModuleRegistry
{
    private $modules;

    public function __construct()
    {
        $this->modules = array();
    }

    public function register($service_id)
    {
        $this->modules[] = $service_id;
    }

    public function getModuleList()
    {
        return $this->modules;
    }
}

