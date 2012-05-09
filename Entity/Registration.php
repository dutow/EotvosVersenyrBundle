<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Minimal class for user registration to a term.
 *
 * @ORM\Table()
 * @ORM\Entity
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
}