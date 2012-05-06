<?php

namespace Eotvos\VersenyrBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity\Round;

/**
 * Loads first admin user
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class RoundFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $round = new Round();

        $start = new \DateTime();
        $start->modify("-2 days");
        $stop = new \DateTime();
        $stop->modify("20 days");

        $round->setRoundtype('eotvos.versenyr.round.upload');
        $round->setPublicity('public');
        $round->setStart($start);
        $round->setStop($stop);
        $round->setAdvanceNo(10);
        $round->setConfig('{}');
        $round->setPage($this->getReference('example-page-round'));

        $om->persist($round);

        $this->setReference('example-round', $round);

        $om->flush();
    }

    /**
     * getOrder
     * 
     * @return int
     */
    public function getOrder()
    {
        return 5;
    }

}

