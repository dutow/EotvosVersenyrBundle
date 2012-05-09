<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a round of a section.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eotvos\VersenyrBundle\Entity\RoundRepository")
 */
class Round
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
     * @var string $roundtype
     *
     * @ORM\Column(name="roundtype", type="string", length=32)
     */
    private $roundtype;

    /**
     * @var string $publicity
     *
     * @ORM\Column(name="publicity", type="string", length=32)
     */
    private $publicity;

    /**
     * @var datetime $start
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var datetime $stop
     *
     * @ORM\Column(name="stop", type="datetime")
     */
    private $stop;

    /**
     * @var integer $advanceNo
     *
     * @ORM\Column(name="advance_no", type="integer")
     */
    private $advanceNo;

    /**
     * @var text $config
     *
     * @ORM\Column(name="config", type="text")
     */
    private $config;

    /**
     * @var object $page
     *
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * @ORM\OneToOne(targetEntity="TextPage", inversedBy="round")
     */
    private $page;

    /**
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="round_id")
     */
    private $submissions;

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
     * Set roundtype
     *
     * @param string $roundtype string represented type of the round.
     */
    public function setRoundtype($roundtype)
    {
        $this->roundtype = $roundtype;
    }

    /**
     * Get roundtype
     *
     * @return string 
     */
    public function getRoundtype()
    {
        return $this->roundtype;
    }

    /**
     * Set publicity
     *
     * @param string $publicity publicity level of the round results.
     */
    public function setPublicity($publicity)
    {
        $this->publicity = $publicity;
    }

    /**
     * Get publicity
     *
     * @return string 
     */
    public function getPublicity()
    {
        return $this->publicity;
    }

    /**
     * Set start
     *
     * @param datetime $start when the round starts.
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Get start
     *
     * @return datetime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set stop
     *
     * @param datetime $stop when the round ends.
     */
    public function setStop($stop)
    {
        $this->stop = $stop;
    }

    /**
     * Get stop
     *
     * @return datetime 
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * Set advanceNo
     *
     * @param integer $advanceNo how many will advance to the next round
     */
    public function setAdvanceNo($advanceNo)
    {
        $this->advanceNo = $advanceNo;
    }

    /**
     * Get advanceNo
     *
     * @return integer 
     */
    public function getAdvanceNo()
    {
        return $this->advanceNo;
    }

    /**
     * Set config
     *
     * @param text $config configuration, type specific
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Get config
     *
     * @return text 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set page
     *
     * @param Eotvos\VersenyrBundle\Entity\TextPage $page page of the round
     */
    public function setPage(\Eotvos\VersenyrBundle\Entity\TextPage $page)
    {
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return Eotvos\VersenyrBundle\Entity\TextPage 
     */
    public function getPage()
    {
        return $this->page;
    }


    /**
     * tells if the round is currently in progress
     * 
     * @return boolean
     */
    public function isActive()
    {
        $now = new \DateTime();

        return ($now > $this->getStart() && $now < $this->getStop());
    }

    /**
     * Tellsif the round is over.
     * 
     * @return void
     */
    public function isDone()
    {
        $now = new \DateTime();

        return ($now > $this->getStop());
    }


    /**
     * Returns a human readable string about the round.
     * 
     * @return void
     *
     * @todo refactor into translations.
     */
    public function getRoundtypeDisplay()
    {
        switch($this->getRoundtype()){
        case 'quiz': return 'Kvíz';

        case 'upload': return 'Esszé';

        case 'final': return 'Döntő';

        case 'info': return 'Elektronikus';
        }
    }

    /**
     * Default constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->submissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setConfig('{}');
    }

    public function getSection()
    {
        return $this->getPage()->getParent()->getSection();
    }


    /**
     * Adds a submission.
     *
     * @param Eotvos\VersenyrBundle\Entity\Submission $submissions user submission
     */
    public function addSubmission(\Eotvos\VersenyrBundle\Entity\Submission $submissions)
    {
        $this->submissions[] = $submissions;
    }

    /**
     * Get submissions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }

    public function getRoundtypeProvider($container)
    {
        return $container->get($this->getRoundtype());
    }

    public function __toString()
    {
        return $this->getPage()->getTitle();
    }

    public function getTerm()
    {
        return $this->getPage()->getParent()->getParent()->getParent()->getTerm();
    }

    public function isFirst()
    {
        return $this->getPage()->isFirstChild();
    }
}
