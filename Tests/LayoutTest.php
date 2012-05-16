<?php

namespace Eotvos\EjtvBundle\Tests\Controller;

use Cancellar\CommonBundle\Test\ModelWebTestCase;

/**
 * Simple tests for the postalcode controller
 * 
 * @uses ModelWebTestCase
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class LayoutTest extends ModelWebTestCase
{
    /**
     * Database setup
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp(__DIR__ ."/../TestFixtures");
    }

    public function testRedirect()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testMenuSize()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/Example%20term/root-page');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(4,$crawler->filter('li.main-menu-item')->count());

    }

    public function testLoginform()
    {
        // should work on multiple pages..
        $targets = array(
            '/Example%20term/root-page',
            '/archives',
            '/Example%20term/szekciok',
        );

        $client = static::createClient();

        foreach ($targets as $target) {
            $client->restart();
            $crawler = $client->request('GET', $target);
            $this->assertTrue($client->getResponse()->isSuccessful());
            $this->assertTrue($crawler->filter('div#login-head')->count() == 1);
            $this->assertTrue($crawler->filter('div#login-head input[type=password]')->count() == 1);
            $this->assertTrue($crawler->filter('div#login-head input[type=text]')->count() == 1);
            $this->assertTrue($crawler->filter('div#login-head input[type=submit]')->count() == 1);

            $form = $crawler->selectButton('loginbtn')->form();
            $crawler = $client->submit($form, array('_username' => 'admin@example.com', '_password' => 'nimda'));
            $this->assertTrue($client->getResponse()->isRedirect()); // login redirect
            $crawler = $client->followRedirect();
            $this->assertTrue($client->getResponse()->isRedirect('/archives'));
            $crawler = $client->followRedirect();
            $this->assertTrue($client->getResponse()->isSuccessful());
        }
    }

}

