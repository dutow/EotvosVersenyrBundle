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
class PageTest extends ModelWebTestCase
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

    public function testSections()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/Example%20term/szekciok');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1,$crawler->filter('a.sectionbox')->count());
        $this->assertEquals(2,$crawler->filter('#breadcrumb li')->count());

    }

    public function testSection()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/Example%20term/szekcio/example-section');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1,$crawler->filter('h2')->count());
        $this->assertRegExp('/Forduló folyamatban/', $client->getResponse()->getContent());
        $this->assertRegExp('/Lorem ipsum stbstb/', $client->getResponse()->getContent());
        $this->assertRegExp('/Jelentkezési hataridő/', $client->getResponse()->getContent());
        $this->assertRegExp('/Example round/', $client->getResponse()->getContent());
        $this->assertEquals(2,$crawler->filter('div.standardround a')->count());
        $this->assertEquals(3,$crawler->filter('#breadcrumb li')->count());

    }

    public function testRound()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/Example%20term/szekcio/example-section/fordulo/example-round');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1,$crawler->filter('h2')->count());
        $this->assertRegExp('/Forduló folyamatban/', $client->getResponse()->getContent());
        $this->assertRegExp('/Lorem ipsum stbstb/', $client->getResponse()->getContent());
        $this->assertRegExp('/Jelentkezési hataridő/', $client->getResponse()->getContent());
        $this->assertRegExp('/Example round/', $client->getResponse()->getContent());
        $this->assertEquals(2,$crawler->filter('div.standardround a')->count());
        $this->assertEquals(4,$crawler->filter('#breadcrumb li')->count());

    }

    public function testSum()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/Example%20term/szekcio/example-section/fordulo/example-round/sum');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1,$crawler->filter('h2')->count());
        $this->assertRegExp('/Forduló folyamatban/', $client->getResponse()->getContent());
        $this->assertRegExp('/Jelentkezési hataridő/', $client->getResponse()->getContent());
        $this->assertRegExp('/Example round/', $client->getResponse()->getContent());
        $this->assertRegExp('/Eredmények/', $client->getResponse()->getContent());
        $this->assertEquals(2,$crawler->filter('div.standardround a')->count());
        $this->assertEquals(4,$crawler->filter('#breadcrumb li')->count());

    }

}

