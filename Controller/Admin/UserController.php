<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Minimal admin interface.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @Route("/admin/user");
 */
class UserController extends Controller
{
    /**
     * Shows user list
     *
     * @return array template parameters
     *
     * @Route("/", name = "admin_user_index" )
     * @Template
     */
    public function indexAction()
    {
        return array(
        );
    }

}
