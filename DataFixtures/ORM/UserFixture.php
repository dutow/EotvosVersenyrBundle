<?php

namespace Eotvos\VersenyrBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity\User;
use Eotvos\VersenyrBundle\Entity\Registration;

/**
 * Loads first admin user
 * 
 * @uses FixtureInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class UserFixture implements FixtureInterface, ContainerAwareInterface
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
        $user = new User();

        $user->setEmail('admin@example.com');
        $user->setAdmin(true);
        $user->setTester(true);
        $passwordGenerator = $this->container->get('eotvos.versenyr.password_generator');
        $passwordGenerator->encodePassword($user, "nimda");

        $om->persist($user);

        $om->flush();
    }

}

