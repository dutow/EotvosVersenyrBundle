<?php

namespace Eotvos\EjtvBundle\TestFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Eotvos\VersenyrBundle\Entity\Section;

/**
 * Loads sample postal codes for tests
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class SectionFixture implements FixtureInterface
{
    /**
     * load
     * 
     * @param ObjectManager $om database object manager
     * 
     * @return void
     */
    public function load(ObjectManager $om)
    {
        $sec = new Section();
        $sec->setName('First section');
        $sec->setNotify('me@example.com');
        $sec->setRegistrationUntil(new \DateTime());
        $om->persist($sec);

        $om->flush();
    }

}

