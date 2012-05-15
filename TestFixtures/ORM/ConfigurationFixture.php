<?php

namespace Eotvos\VersenyrBundle\TestFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity\Configuration;

/**
 * Loads first admin user
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class ConfigurationFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $em = $this->container->get('doctrine')->getEntityManager();

        $conf = new Configuration();
        $conf->setName('eotvos.versenyr.sitename');
        $conf->setValue('VersenyR');
        $om->persist($conf);

        $conf = new Configuration();
        $conf->setName('eotvos.versenyr.facebook.page');
        $conf->setValue('http://versenyr.info');
        $om->persist($conf);

        $conf = new Configuration();
        $conf->setName('eotvos.versenyr.admin.contact');
        $conf->setValue('contact@example.com');
        $om->persist($conf);

        $conf = new Configuration();
        $conf->setName('eotvos.versenyr.sender.email');
        $conf->setValue('noreply@example.com');
        $om->persist($conf);

        $conf = new Configuration();
        $conf->setName('eotvos.versenyr.sender.title');
        $conf->setValue('VersenyR Mailer');
        $om->persist($conf);

        $om->flush();
    }

    /**
     * getOrder
     * 
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }

}

