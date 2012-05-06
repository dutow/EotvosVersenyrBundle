<?php

namespace Eotvos\VersenyrBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity\Section;

/**
 * Loads first admin user
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class SectionFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * setContainer
     * 
     * @param ContainerInterface $container service container
     * 
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    /**
     * Loads user fixtures
     * 
     * @param ObjectManager $om object manager
     * 
     * @return void
     */
    public function load(ObjectManager $om)
    {
        $section = new Section();

        $until = new \DateTime();
        $until->modify('+3 days');

        $section->setRegistrationUntil($until);
        $section->setPage($this->getReference('example-page-section'));
        $section->setNotify('notify@example.com');

        $om->persist($section);

        $this->setReference('example-section', $section);

        $om->flush();
    }

    /**
     * getOrder
     * 
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }

}

