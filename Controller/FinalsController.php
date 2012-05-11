<?php
/**
 * Declaration of UploadRoundController class.
 *
 * @category EotvosVerseny
 * @package Controller
 * @author Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2011, Cancellar Informatikai Bt
 * @license http://www.opensource.org/licenses/BSD-2-Clause
 */

namespace Eotvos\VersenyrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerAware;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Eotvos\VersenyBundle\Entity\Submission;
use Eotvos\VersenyBundle\Entity\UploadRoundSecurityToken;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Response;


use Eotvos\VersenyBundle\Form\SimpleFileForm;
use Eotvos\VersenyBundle\Extension\ExtToMime;

/**
 * Nop controller for finals roundtypes
 * 
 * @uses ContainerAware
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class FinalsController extends ContainerAware
{
  /**
    * Renders a view.
    *
    * Copied from symfony's Controller class.
    *
    * @param string   $view       The view name
    * @param array    $parameters An array of parameters to pass to the view
    * @param Response $response   A response instance
    *
    * @return Response A Response instance
    *
    * @author Fabien Potencier <fabien@symfony.com>
    * @version 2011-10-11
    * @since   2011-10-11
    */
  protected function render($view, array $parameters = array(), Response $response = null)
  {
      return $this->container->get('templating')->renderResponse($view, $parameters, $response);
  }

  /**
   * Renders the content for the round description panel.
   *
   * @param Round $round questioned object
   *
   * @return  array of template parameters
   *
   * @Template()
   **/
  public function activeDescriptionAction($round)
  {
    $user = $this->container->get('security.context')->getToken()->getUser();

    // term is not required because this doesn't includes the layout
    return array(
        'round' => $round->getRound(),
        'spec' => json_decode($round->getRound()->getConfig()),
        'user' => $user,
    );
  }

}
