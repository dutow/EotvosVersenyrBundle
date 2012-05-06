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
 * Imports or updates a site structire from a YAML file. This was primarily used before an admin interface was
 * developed. Should work even now, but since the structure is complex, for an example, see fixtures/2011.yml.
 * 
 * @uses ContainerAwareCommand
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class YearCommand extends ContainerAwareCommand
{

    // contains the last section-like entry
    private $sectionshead;

    /**
     * Command configuration
     * 
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ecv:year')
            ->setDescription('Creates the root nodes of a new year')
            ->addArgument('yml', InputArgument::REQUIRED, 'Source file')
            ;
    }

    /**
     * Creates a textpage
     * 
     * @param string   $slug    sluggable name
     * @param array    $ymldata textpage information
     * @param TextPage $root    parent page or null
     * 
     * @return void
     */
    protected function processTextPage($slug, $ymldata, $root)
    {
        $tp = $this->em->getRepository('EotvosVersenyrBundle:TextPage')->getChildWithTitle($root, $ymldata['title']);
        if (isset($ymldata['newtitle'])) {
            $ymldata['title'] = $ymldata['newtitle'];
        }
        if (isset($ymldata['delete']) && $ymldata['delete'] == true) {
            if ($tp) {
                $this->em->remove($tp);
            }

            return null;
        }
        if (!$tp) {
            $tp = new Entity\TextPage();
        }
        $tp->setTitle(@$ymldata['title']);
        $tp->setRedirectsTo(@$ymldata['redirects_to']);
        $tp->setBody(@$ymldata['body']);
        $tp->setSlug($slug);
        $tp->setSpecial(@$ymldata['special']);
        $tp->setParent($root);
        if (isset($ymldata['fbbox']) && $ymldata['fbbox']=='yes') {
            $tp->setFbbox(true);
        } else {
            $tp->setFbbox(false);
        }
        if (isset($ymldata['inmenu']) && $ymldata['inmenu']=='no') {
            $tp->setInMenu(false);
        } else {
            $tp->setInMenu(true);
        }
        $this->em->persist($tp);
        $this->em->flush();

        if ($tp->getSpecial()=='sections') {
            $this->sectionshead = $tp;
        }

        if (isset($ymldata['children'])) {
            foreach ($ymldata['children'] as $slug => $child) {
                $this->processTextPage($slug, $child, $tp);
            }
        }

        return $tp;

    }

    /**
     * Creates a section.
     * 
     * @param TextPage $textpage Container textpage
     * @param array    $sectiond Section information
     * 
     * @return void
     */
    protected function createSection($textpage, $sectiond)
    {
        if ($textpage->getSection()) {
            $section = $textpage->getSection();
        } else {
            $section = new Entity\Section();
        }
        $section->setRegistrationUntil(new \DateTime($sectiond['registration_until']));

        $notifiers = array();
        if ($sectiond['notify_mail'] && is_array($sectiond['notify_mail'])) {
            foreach ($sectiond['notify_mail'] as $mail) {
                foreach (mailparse_rfc822_parse_addresses($mail) as $addr) {
                    $notifiers[]= array($addr['display'], $addr['address']);
                }
            }
        }
        $section->setNotify(json_encode($notifiers));


        $section->setPage($textpage);
        $this->em->persist($section);
        $this->em->flush();
    }

    /**
     * Processes a round record.
     * 
     * @param TextPage $textpage container textpage
     * @param array    $roundd   round information
     * 
     * @return void
     */
    protected function processRound($textpage, $roundd)
    {
        if ($textpage->getRound()) {
            $round = $textpage->getRound();
        } else {
            $round = new Entity\Round();
        }
        //    $round->setAdvanceNo($roundd['advance_no']);
        $round->setPublicity($roundd['publicity']);
        $round->setRoundtype($roundd['roundtype']);
        $round->setConfig(json_encode($roundd['config']));
        $round->setStart(new \DateTime($roundd['start']));
        $round->setStop(new \DateTime($roundd['stop']));
        $round->setPage($textpage);
        $this->em->persist($round);
        $this->em->flush();
    }

    /**
     * Imports or updates pages.
     * 
     * @param InputInterface  $input  Standard input
     * @param OutputInterface $output Standard output
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();

        $ymlfn = $input->getArgument('yml');
        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents($ymlfn));

        foreach ($data as $year => $oneyear) {

            $root = $this->em->getRepository('EotvosVersenyrBundle:TextPage')->getYearRoot((string) $year);
            if (!$root) {
                $root = new Entity\TextPage();
                $root->setTitle($year);
                $root->setSlug($year);
                $root->setFbbox(false);
                $root->setSpecial('ROOT');
                $root->setInMenu(false);
                $this->em->persist($root);
                $this->em->flush();
            }

            if (isset($oneyear['pages']) && is_array($oneyear['pages'])) {
                foreach ($oneyear['pages'] as $slug => $textpage) {
                    $this->processTextPage($slug, $textpage, $root);
                }
            }

            if (isset($oneyear['sections']) && is_array($oneyear['sections'])) {
                foreach ($oneyear['sections'] as $slug => $section) {
                    if ($this->sectionshead) {
                        $tp = $this->processTextPage($slug, $section, $this->sectionshead);
                    } else {
                        $tp = $this->processTextPage($slug, $section, $root);
                    }
                    if ($tp) {
                        $sectionhh = $this->createSection($tp, $section);
                    }
                    if (isset($section['rounds']) && is_array($section['rounds'])) {
                        $i = 0;
                        foreach ($section['rounds'] as $round) {
                            $i = $i + 1;
                            $data = array();
                            $data['slug'] = $tp->getSlug().'_'.$i;
                            $data['title'] = $round['name'];
                            $data['body'] = $round['description'];
                            $data['children'] = @$round['children'];
                            $data['fbbox'] = @$round['fbbox'];

                            $tpp = $this->processTextPage($data['slug'], $data, $tp);
                            if ($tpp) {
                                $round = $this->processRound($tpp, $round);
                            }
                        }
                    }
                }
            }

        }
    }

}
