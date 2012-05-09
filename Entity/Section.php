<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eotvos\VersenyrBundle\Entity\Section
 *
 * @ORM\Entity(repositoryClass="Eotvos\VersenyrBundle\Entity\SectionRepository")
 * @ORM\Table(
 * )
 */
class Section
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
     * @var string $registrationUntil
     *
     * @ORM\Column(name="registration_until", type="date")
     */
    private $registrationUntil;

    /**
     * @var object $page
     *
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * @ORM\OneToOne(targetEntity="TextPage", inversedBy="section")
     */
    private $page;

    /**
     * @var string $notify
     *
     * @ORM\Column(name="notify", type="text")
     */
    private $notify;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Registration", mappedBy="sections")
     */
    private $attendees;

    /**
     * Default constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->attendees = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set registrationUntil
     *
     * @param string $registrationUntil registration deadline
     */
    public function setRegistrationUntil($registrationUntil)
    {
        $this->registrationUntil = $registrationUntil;
    }

    /**
     * Get registrationUntil
     *
     * @return string 
     */
    public function getRegistrationUntil()
    {
        return $this->registrationUntil;
    }

    /**
     * Add attendees
     *
     * @param Eotvos\VersenyrBundle\Entity\User $attendee user registered to the section
     */
    public function addUser(\Eotvos\VersenyrBundle\Entity\User $attendee)
    {
        $this->attendees[] = $attendee;
    }

    /**
     * Get attendees
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * Set page
     *
     * @param Eotvos\VersenyrBundle\Entity\TextPage $page page of the section
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
     * Gets the string representation of the section, which is it's title.
     * 
     * @return void
     */
    public function __toString()
    {
        return $this->getPage()->getTitle();
    }


    /**
     * Returns the next round for the section if any, based on the current date.
     * 
     * @return void
     *
     * @todo date as parameter
     */
    public function nextRound()
    {
        $now = new \DateTime();
        foreach ($this->page->getChildren() as $round) {
            if ($now < $round->getRound()->getStop()) {
                return $round->getRound();
            }
        }

        return null;
    }


    /**
     * Set notify
     *
     * @param text $notify who to notify for new registrants.
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    /**
     * Get notify
     *
     * @return text 
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * Returns json decoded notification struct.
     * 
     * @return void
     */
    public function getDecodedNotify()
    {
        return json_decode($this->getNotify());
    }

}
