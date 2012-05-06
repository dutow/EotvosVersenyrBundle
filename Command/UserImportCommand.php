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
 * Imports users from a CSV file.
 *
 * This is a legacy command which was used to imports the users from the original EJTV site into this new version. Since
 * this was a special case, this command cannot be used as a generic solution, but it can be a good example on how to
 * do this.
 * 
 * @uses ContainerAwareCommand
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class UserImportCommand extends ContainerAwareCommand
{

    /**
     * Command configuration.
     * 
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ecv:userimport')
            ->setDescription('Imports users from the previous site')
            ->addArgument('csv', InputArgument::REQUIRED, 'Source file')
            ;
    }

    /**
     * Adds a section to a user.
     * 
     * @param User $user targeted user
     * @param int  $id   section id
     * 
     * @return User
     */
    protected function processSection($user, $id)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $sm = $em->getRepository('EotvosVersenyrBundle:Section');
        $user->addSection($sm->findOneById($id));

        return $user;
    }

    /**
     * Imports users from a file.
     * 
     * @param InputInterface  $input  Standard input
     * @param OutputInterface $output Standard output
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $um = $em->getRepository('EotvosVersenyrBundle:User');

        $sm = $em->getRepository('EotvosVersenyrBundle:School');

        $pm = $em->getRepository('EotvosVersenyrBundle:Postalcode');

        $cmp = $em->getRepository('EotvosVersenyrBundle:Country');
        $hungaryRec = $cmp->findOneByName('Magyarország');

        $csvfn = $input->getArgument('csv');

        if (($handle = fopen($csvfn, "r")) !== false) {
            $first = true;
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if ($first) {
                    // skip header line
                    $first = false;
                    continue;
                }

                    foreach ($data as $k => $v) {
                        $data[$k] = trim($v); // stupid spaces
                    }

                $email = $data[1];
                $output->writeln("");
                $output->writeln("====================");
                $output->writeln("E-mail: ".$email);
                if ($um->findOneByEmail($email)) {
                    $output->writeln("Found already in DB, NEXT!");
                    continue;
                }
                $u = new Entity\User();
                $u->setEmail($email);

                $u->setYear(2011);
                $u->setCountryId($hungaryRec); // todo: edit others

                $name = explode(' ', $data[0], 2);
                $output->writeln("First name: ".$name[1]);
                $u->setFirstName($name[1]);
                $output->writeln("Last name: ".$name[0]);
                $u->setLastName($name[0]);

                $output->writeln("Address: ".$data[2]);
                $u->setAddress($data[2]);

                $city = $data[3];
                $zip = $data[4];
                if ($zip==0) {
                    if ($city=="") {
                        $output->writeln("No place information, NOT BUG");
                    } else {
                        $output->writeln("Zero zip with city: ".$city);
                        $u->setOtherCity($city); // majd kezi javitas
                    }
                } else {
                    if ($city=="") {
                    } else {
                        $prefs = $pm->getWithPrefix($zip);
                        $gprs = array();
                        $cns = array();
                        foreach ($prefs as $pref) {
                            if (stristr(mb_strtoupper($pref['name']), mb_strtoupper($city))) {
                                $cns[]= $pref['name'];
                                $gprs[]= $pref;
                            }
                        }
                        $output->writeln("Found : ".count($gprs)." possibility for name ".$city." as: ".implode('|', $cns));
                        if (count($gprs)>1) {
                            // TODO!
                            continue;
                        }
                        if (count($gprs)==1) {
                            $u->setPostalcode($gprs[0]['code']);
                        }
                    }
                }

                $schoolname = $data[5];
                $schools = $sm->getWithPrefix($schoolname);
                $output->writeln("Found ".count($schools)." school with prefix ".$schoolname);
                if (count($schools)!=1) {
                    $u->setOtherSchool($schoolname);
                } else {
                    $u->setSchoolId($sm->findOneById($schools[0]['id']));
                }

                $factory = $this->getContainer()->get('security.encoder_factory');
                $encoder = $factory->getEncoder($u);
                $chars = "234567890abcdefghijkmnoprstuvwxABCDEFGHIJKLMNOPRSTUVWX";
                $length = 8;
                $i = 0;
                $password = "";
                while ($i <= $length) {
                    $password .= $chars{mt_rand(0, strlen($chars)-1)};
                    $i++;
                }
                $salt = "";
                $i = 0;
                while ($i <= $length) {
                    $salt .= $chars{mt_rand(0, strlen($chars)-1)};
                    $i++;
                }
                $u->setSalt($salt);
                $u->setPassword($encoder->encodePassword($password, $salt));

                $u->setSchoolYear($data[9]);
                $output->writeln("School Year: ".$data[9]);
                $u->setSchoolTeacher($data[10]);
                $output->writeln("School teacher: ".$data[10]);
                $u->setSchoolTeacherContact($data[11]);
                $output->writeln("School teacher contact: ".$data[11]);


                $sections = array();

                if ($data[12]!="") {
                    $this->processSection($u, 1); //Angol 
                }
                if ($data[13]!="") {
                    $this->processSection($u, 2);// Biz
                }
                if ($data[14]!="") {
                    $this->processSection($u, 3);// filo
                }
                if ($data[15]!="") {
                    $this->processSection($u, 4);// fizika
                }
                if ($data[16]!="") {
                    $this->processSection($u, 5);// foldrajz
                }
                if ($data[17]!="") {
                    $this->processSection($u, 6);// francia
                }
                if ($data[18]!="") {
                    $this->processSection($u, 7);// informatika
                }
                if ($data[19]!="") {
                    $this->processSection($u, 8);// latin
                }
                if ($data[20]!="") {
                    $this->processSection($u, 9);// magyar
                }
                if ($data[21]!="") {
                    $this->processSection($u, 10);// matek
                }
                if ($data[22]!="") {
                    $this->processSection($u, 11);// nemet
                }
                if ($data[23]!="") {
                    $this->processSection($u, 12);// ogorog
                }
                if ($data[24]!="") {
                    $this->processSection($u, 13);// oriental
                }
                if ($data[25]!="") {
                    $this->processSection($u, 14);// tarstud
                }

                $em->persist($u);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Eötvös József Tanulmányi Verseny Emlékeztető')
                    ->setFrom('verseny@eotvos.elte.hu')
                    ->setTo($u->getEmail())
                    ->setBody($this->getContainer()->get('templating')->render('EotvosVersenyrBundle:User:email_import.txt.twig', array('user' => $u, 'password' => $password)))
                    ;
                $this->getContainer()->get('mailer')->send($message);
            }
            fclose($handle);
        }
    }

}
