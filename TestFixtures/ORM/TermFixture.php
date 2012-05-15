<?php

namespace Eotvos\VersenyrBundle\TestFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity\Term;

/**
 * Loads first admin user
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class TermFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $term = new Term();

        $start = new \DateTime();
        $start->modify("-10 days");

        $term->setName('Example term');
        $term->setActive(true);
        $term->setRegistrationStart($start);
        $term->setUserType('eotvos.versenyr.usertype.dummy');
        $term->generateChildren($em);

        $om->persist($term);

        $this->setReference('example-term', $term);

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

