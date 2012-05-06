<?php

namespace Eotvos\VersenyrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

use Eotvos\VersenyrBundle\Entity as Entity;

/**
 * Generates a file structure containing all uploaded files for all sections in a given round.
 *
 * This is a somewhat legacy command which was used in the 2011 EJTV because the lack of an admin interface. It can be
 * used in the future if required, e.g. to ensure anonymiti of the contensants. For this case, this taks should be
 * refactored, and placed in the admin interface as a button.
 *
 * The task requires three parameters:
 * * year on which it is run
 * * round number
 * * target folder
 * 
 * @uses ContainerAwareCommand
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class SectionZipCommand extends ContainerAwareCommand
{

    /**
     * Command configration.
     * 
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ecv:section:zipper')
            ->setDescription('Genearates an archive for a section round.')
            ->addArgument('year', InputArgument::REQUIRED, 'Year')
            ->addArgument('round', InputArgument::REQUIRED, 'Round number')
            ->addArgument('path', InputArgument::REQUIRED, 'Target directory')
            ;
    }


    /**
     * Copies uploaded files to the specified directory.
     * 
     * @param InputInterface  $input  Standard input
     * @param OutputInterface $output Standard output
     * 
     * @return void
     *
     * @todo Check hardcoded paths
     * @todo Refactor unix-style slashes to generic
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $sm = $em->getRepository('EotvosVersenyrBundle:Section');

        $sectionRec = null;
        foreach ($sm->getForYear($input->getArgument('year')) as $section) {
            $sectionRec = $section;

            $roundRec = null;
            $i = 0;
            foreach ($sectionRec->getPage()->getChildren() as $round) {
                $i++;
                if ($i == $input->getArgument('round')) {
                    $roundRec = $round->getRound();
                    break;
                }
            }
            if ($roundRec==null) {
                $output->writeln("Round not found for section $section");
                continue;
            }

            if ($roundRec->getRoundtype()!="upload") {
                $output->writeln("Round not found for section $section");
                continue;
            }
            $submissions = $roundRec->getSubmissions();

            $lastSubmissions = array();
            foreach ($submissions as $submission) {
                if (!isset($lastSubmissions[$submission->getUserId()->getUniqueIdentifier()])) {
                    $lastSubmissions[$submission->getUserId()->getId()] = array();
                }
                $userSubm =& $lastSubmissions[$submission->getUserId()->getUniqueIdentifier()];

                $key = $submission->getCategory();
                if ($key=="") {
                    $key = "undefined";
                }
                if ($key=="undefined") {
                    $key = "solution";
                }
                if (
                    (isset($userSubm[$key]) && $userSubm[$key]->getSubmittedAt() < $submission->getSubmittedAt())
                    ||
                    (!isset($userSubm[$key]))
                ) {
                    $userSubm[$key] = $submission;
                }
            }

            $pathBase = $input->getArgument('path');
            foreach ($lastSubmissions as $user => $categories) {
                foreach ($categories as $catName => $subm) {
                    $subDat = json_decode($subm->getData());
                    $fileExt = strtolower(pathinfo($subDat->filename, PATHINFO_EXTENSION));
                    $orig = '../../beta.verseny.eotvos.elte.hu/app/uploads/'.$subm->getId().'.'.$fileExt;
                    if (!file_exists($orig)) {
                        $orig = str_replace('docx', 'zip', $orig);
                    }
                    if (!file_exists($orig)) {
                        $orig = '../../beta.verseny.eotvos.elte.hu/app/uploads/'.$subm->getId().'.';
                    }
                    $toPath = $pathBase.'/'.$section.'/';
                    if (!file_exists($toPath)) {
                        mkdir($toPath, 0777, true);
                    }
                    $to = $toPath.$user.'-'.$catName.'.'.$fileExt;
                    copy($orig, $to);
                    $output->writeln("Copied from $orig to $to");
                }
            }
        }

    }

}
