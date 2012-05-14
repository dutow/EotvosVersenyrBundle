<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Eotvos\VersenyrBundle\Entity\Configuration;
use Eotvos\VersenyrBundle\Form\Type\ConfigurationType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Minimal admin interface for configurations.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @Route("/admin/configuration");
 */
class ConfigurationController extends Controller
{
    /**
     * Shows configuration list
     *
     * @return array template parameters
     *
     * @Route("/", name = "admin_configuration_index" )
     * @Template
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Configuration')
            ;

        $request = $this->get('request');
        $type = $request->query->get('type', 'all');

        if (!in_array($type, array('all'))) {
            throw $this->createNotFoundException("Bad parameter");
        }

        switch ($type) {

            case 'all':
                $configurations = $repo->findAll();
                break;
        }

        return array(
            'configurations' => $configurations,
        );
    }

    /**
     * Editing a configuration
     *
     * @param int $id Id of the configuration
     *
     * @return array of template parameters
     *
     * @Route("/edit/{id}", name = "admin_configuration_edit" )
     * @Template
     */
    public function editAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Configuration not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Configuration')
            ;

        $configuration = $repo->findOneById($id);

        if (!$configuration) {
            throw $this->createNotFoundException("Configuration not found");
        }

        $form = $this->createForm(new ConfigurationType($this->container), $configuration);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $configuration = $form->getData();

                if ($configuration->getId()!=$id) {
                    throw $this->createNotFoundException("Don't forge stupid updates");
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_configuration_index'));
            }
        }

        return array(
            'configuration' => $configuration,
            'form' => $form->createView(),
        );
    }

}
