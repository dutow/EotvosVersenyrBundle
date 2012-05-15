<?php

namespace Eotvos\VersenyrBundle\Tests\Controller;

use Cancellar\CommonBundle\Test\ModelWebTestCase;

use Eotvos\VersenyrBundle\Extension\ExtToMime;

/**
 * Simple tests for the postalcode controller
 * 
 * @uses ModelWebTestCase
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class ExtToMimeTest extends ModelWebTestCase
{
    /**
     * Database setup
     * 
     * @return void
     */
    public function setUp()
    {
        parent::setUp(__DIR__ ."/../TestFixtures");

        // most important mime types
        $this->targets = array(
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'html' => 'text/html',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'zip' => 'application/zip',
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'tiff' => 'image/tiff',
            'rar' => 'application/rar',
        );
    }

    public function testMimeToExtension()
    {
        $em = new ExtToMime();

        foreach ($this->targets as $key => $target) {
            $this->assertEquals($key, $em->system_mime_type_extension($target));
        }
    }

    public function testExtensionToMime()
    {
        $em = new ExtToMime();

        foreach ($this->targets as $key => $target) {
            $this->assertEquals($target, $em->system_extension_mime_type($key));
        }
        // additional one direction tests
        $this->assertEquals('text/plain', $em->system_extension_mime_type('txt'));
        $this->assertEquals('image/jpeg', $em->system_extension_mime_type('jpg'));
    }

}

