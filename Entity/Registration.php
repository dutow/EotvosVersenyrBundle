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
     * @ORM\Column(name="term", type="integer")
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
}
