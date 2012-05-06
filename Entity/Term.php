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
}
