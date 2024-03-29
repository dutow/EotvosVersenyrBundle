<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Minimal class for user registration to a term.
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="regtype", type="string")
 * @ORM\DiscriminatorMap({"registration" = "Eotvos\VersenyrBundle\Entity\Registration", "school" = "Eotvos\DemoBundle\Entity\SchoolRegistration"})
 */
class Registration
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
     * @var integer $term
     *
     * @ORM\JoinColumn(name="term_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Term", inversedBy="registrations")
     */
    private $term;

    /**
     * @var integer $term
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="registrations")
     */
    private $user;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Section", inversedBy="registrations")
     * @ORM\JoinTable(name="section_registrations")
     */
    private $sections;

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
     * Set term
     *
     * @param integer $term
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     * Get term
     *
     * @return integer 
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set user
     *
     * @param Eotvos\VersenyrBundle\Entity\User $user
     */
    public function setUser(\Eotvos\VersenyrBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Eotvos\VersenyrBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __construct()
    {
        $this->sections = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add sections
     *
     * @param Eotvos\VersenyrBundle\Entity\Section $sections
     */
    public function addSection(\Eotvos\VersenyrBundle\Entity\Section $sections)
    {
        $this->sections[] = $sections;
    }

    /**
     * Set sections
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function setSections($sections)
    {
        return $this->sections = $sections;
    }

    /**
     * Get sections
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSections()
    {
        return $this->sections;
    }

    public function getTermName()
    {
        return $this->getTerm()->getName();
    }

    /**
     * Did the user join the given section?
     *
     * @param Section $section question subject
     * 
     * @return bool
     */
    public function hasSection(\Eotvos\VersenyrBundle\Entity\Section $section)
    {
        foreach ($this->getSections() as $sec) {
            if ($sec->getId() == $section->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * May the user join the given section?
     *
     * @param Section $section question subject
     *
     * @return bool true if possible
     */
    public function mayJoin(\Eotvos\VersenyrBundle\Entity\Section $section)
    {
        $now = new \DateTime();
        $now->sub(new \DateInterval('P1D'));

        return ($section->getRegistrationUntil() > $now);
    }
}
