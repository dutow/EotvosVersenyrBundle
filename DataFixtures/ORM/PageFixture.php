<?php

namespace Eotvos\VersenyrBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity\TextPage;

/**
 * Loads first admin user
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class TextPageFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $page1 = new TextPage();
        $page1->setTitle('Example page');
        $page1->setSlug('root-page');
        $page1->setRoot(null);
        $page1->setFbbox(false);
        $page1->setInMenu(true);
        $page1->setBody("Lorem ipsum stbstb");
        $om->persist($page1);
        $om->flush();
        $this->setReference('example-page-root', $page1);

        $page2 = new TextPage();
        $page2->setTitle('Example section page');
        $page2->setSlug('section-page');
        $page2->setParent($page1);
        $page2->setFbbox(false);
        $page2->setInMenu(true);
        $page2->setBody("Lorem ipsum stbstb");
        $om->persist($page2);
        $om->flush();
        $this->setReference('example-page-section', $page2);

        $page3 = new TextPage();
        $page3->setTitle('Example round page');
        $page3->setSlug('round-page');
        $page3->setParent($page2);
        $page3->setFbbox(false);
        $page3->setInMenu(false);
        $page3->setBody("Lorem ipsum stbstb");
        $om->persist($page3);
        $om->flush();
        $this->setReference('example-page-round', $page3);
    }

    /**
     * getOrder
     * 
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

}

