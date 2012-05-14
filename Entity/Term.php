<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eotvos\VersenyrBundle\Entity\Term
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eotvos\VersenyrBundle\Entity\TermRepository")
 */
class Term
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string $userType
     *
     * @ORM\Column(name="userType", type="string", length=128)
     */
    private $userType;

    /**
     * @var integer $rootPage
     *
     * @ORM\ManyToOne(targetEntity="TextPage")
     * @ORM\JoinColumn(name="rootPageId", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $rootPage;

    /**
     * @var datetime $registrationStart
     *
     * @ORM\Column(name="registrationStart", type="datetime")
     */
    private $registrationStart;

    /**
     * @ORM\OneToMany(targetEntity="Registration", mappedBy="term")
     */
    private $registrations;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set userType
     *
     * @param string $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /**
     * Get userType
     *
     * @return string 
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set rootPageId
     *
     * @param integer $rootPageId
     */
    public function setRootPage($rootPageId)
    {
        $this->rootPage = $rootPageId;
    }

    /**
     * Get rootPageId
     *
     * @return integer 
     */
    public function getRootPage()
    {
        return $this->rootPage;
    }

    public function getSections()
    {
        $secs = array();
        foreach ($this->getRootPage()->getChildren() as $child) {
            if ('sections'==$child->getSpecial()) {
                foreach ($child->getChildren as $ch) {
                    if ('section'==$child->getSpecial()) {
                        $secs[] =$child->getSection();
                    }
                }
            }

            return $secs;
        }

        return $secs;
    }

    /**
     * Set registrationStart
     *
     * @param datetime $registrationStart
     */
    public function setRegistrationStart($registrationStart)
    {
        $this->registrationStart = $registrationStart;
    }

    /**
     * Get registrationStart
     *
     * @return datetime 
     */
    public function getRegistrationStart()
    {
        return $this->registrationStart;
    }

    public function getRegistrationUntil()
    {
        $until = $this->getRegistrationStart();
        foreach ($this->getSections() as $sec) {
            if ($sec->getRegistrationUntil() > $until) {
                $until = $sec->getRegistrationUntil();
            }
        }

        return $until;
    }

    public function generateChildren($em)
    {
        $root = new TextPage();
        $root->setTitle('Root page');
        $root->setParent(null);
        $root->setSpecial('termroot');
        $root->setInMenu(false);
        $root->setBody("Lorem ipsum stbstb this here is never shown");
        $em->persist($root);

        $this->setRootPage($root);

        $termmail = new TextPage();
        $termmail->setTitle('Welcome');
        $termmail->setParent($root);
        $termmail->setInMenu(false);
        $termmail->setSpecial('termmail');
        $termmail->setBody("Lorem ipsum stbstb");
        $em->persist($termmail);

        $reg1 = new TextPage();
        $reg1->setTitle('Registration');
        $reg1->setParent($root);
        $reg1->setInMenu(true);
        $reg1->setSpecial('register');
        $reg1->setBody("Lorem ipsum stbstb");
        $em->persist($reg1);

        $reg2 = new TextPage();
        $reg2->setTitle('Succesfull registration');
        $reg2->setParent($root);
        $reg2->setInMenu(false);
        $reg2->setSpecial('register_after');
        $reg2->setBody("Lorem ipsum stbstb");
        $em->persist($reg2);

        $reg3 = new TextPage();
        $reg3->setTitle('Succesfull registration');
        $reg3->setParent($root);
        $reg3->setInMenu(false);
        $reg3->setSpecial('register_mail');
        $reg3->setBody("Lorem ipsum stbstb");
        $em->persist($reg3);

        $reg4 = new TextPage();
        $reg4->setTitle('User registration');
        $reg4->setParent($root);
        $reg4->setInMenu(false);
        $reg4->setSpecial('section_notify');
        $reg4->setBody("Lorem ipsum stbstb");
        $em->persist($reg4);

        $reg5 = new TextPage();
        $reg5->setTitle('No registration');
        $reg5->setParent($root);
        $reg5->setInMenu(false);
        $reg5->setSpecial('register_notopen');
        $reg5->setBody("Lorem ipsum stbstb");
        $em->persist($reg5);

        $reg6 = new TextPage();
        $reg6->setTitle('No registration');
        $reg6->setParent($root);
        $reg6->setInMenu(false);
        $reg6->setSpecial('register_done');
        $reg6->setBody("Lorem ipsum stbstb");
        $em->persist($reg6);


        $archiv = new TextPage();
        $archiv->setTitle('Archives');
        $archiv->setParent($root);
        $archiv->setInMenu(true);
        $archiv->setSpecial('archives');
        $archiv->setBody("Lorem ipsum stbstb");
        $em->persist($archiv);

        $sections = new TextPage();
        $sections->setTitle('Sections');
        $sections->setParent($root);
        $sections->setInMenu(true);
        $sections->setSpecial('sections');
        $sections->setBody("Lorem ipsum stbstb");
        $em->persist($sections);

        $section = new TextPage();
        $section->setTitle('Example section');
        $section->setParent($sections);
        $section->setInMenu(false);
        $section->setSpecial('section');
        $section->setBody("Lorem ipsum stbstb");
        $em->persist($section);

        $until = $this->getRegistrationStart();
        $until->modify('+30 days');
        $sec = new Section();
        $sec->setRegistrationUntil($until);
        $sec->setPage($section);
        $sec->setNotify('notify@example.com');
        $em->persist($sec);


        $round = new TextPage();
        $round->setTitle('Example section');
        $round->setParent($section);
        $round->setInMenu(false);
        $round->setSpecial('round');
        $round->setBody("Lorem ipsum stbstb");
        $em->persist($round);

        $until = clone($until);
        $until->modify('-23 days');
        $stop = clone($until);
        $stop->modify('+9 days');
        $rnd = new Round();
        $rnd->setRoundtype('eotvos.versenyr.roundtype.finals');
        $rnd->setConfig('{}');
        $rnd->setStart($until);
        $rnd->setStop($stop);
        $rnd->setPage($round);
        $rnd->setPublicity('eotvos.versenyr.results.private');
        $rnd->setAdvanceNo(10);
        $em->persist($rnd);
    }
    public function __construct()
    {
        $this->registrations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add registrations
     *
     * @param Eotvos\VersenyrBundle\Entity\Registration $registrations
     */
    public function addRegistration(\Eotvos\VersenyrBundle\Entity\Registration $registrations)
    {
        $this->registrations[] = $registrations;
    }

    /**
     * Get registrations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    public function open($date=null)
    {
        if (null===$date) {
            $date = new \DateTime();
        }

        return $date < $this->getRegistrationUntil();
    }
}
