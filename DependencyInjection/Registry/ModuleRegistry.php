<?php
namespace Eotvos\VersenyrBundle\DependencyInjection\Registry;

class ModuleRegistry
{
    private $modules;

    public function __construct()
    {
        $this->modules = array();
    }

    public function register($service_name, $service_type)
    {
        $this->modules[$service_name] = $service_type;
    }

    public function getModuleList()
    {
        return $this->modules;
    }
}

