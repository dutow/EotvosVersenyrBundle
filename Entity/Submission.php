<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eotvos\VersenyrBundle\Entity\Submission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eotvos\VersenyrBundle\Entity\SubmissionRepository")
 */
class Submission
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
     * @var integer $user_id
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="submissions")
     */
    private $user_id;

    /**
     * @var integer $round
     *
     * @ORM\JoinColumn(name="round_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="submissions")
     */
    private $round;

    /**
     * @var boolean $advances
     *
     * @ORM\Column(name="advances", type="integer", nullable=true)
     */
    private $advances;

    /**
     * @var integer $points
     *
     * @ORM\Column(name="points", type="integer", nullable=true)
     */
    private $points;

    /**
     * @var text $data
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    /**
     * @var datetime $submitted_at
     *
     * @ORM\Column(name="submitted_at", type="datetime")
     */
    private $submitted_at;

    /**
     * @var boolean $finalized
     *
     * @ORM\Column(name="finalized", type="boolean")
     */
    private $finalized;

    /**
     * @var boolean $category
     *
     * @ORM\Column(name="category", type="string", nullable=true)
     */
    private $category;

    /**
     * __construct
     * 
     * 
     * @return void
     */
    public function __construct(){
        $this->finalized = false;
        $this->submitted_at = new \DateTime();
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
     * Set user_id
     *
     * @param integer $userId
     */
    public function setUser($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user_id;
    }

    /**
     * Set round_id
     *
     * @param integer $roundId
     */
    public function setRound($roundId)
    {
        $this->round = $roundId;
    }

    /**
     * Get round_id
     *
     * @return integer 
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set advances
     *
     * @param boolean $advances
     */
    public function setAdvances($advances)
    {
        $this->advances = $advances;
    }

    /**
     * Get advances
     *
     * @return boolean 
     */
    public function getAdvances()
    {
        return $this->advances;
    }

    public function advanced()
    {
        return $this->getAdvances()==2;
    }

    public function notAdvanced()
    {
        return $this->getAdvances()==1;
    }

    /**
     * Set points
     *
     * @param integer $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set data
     *
     * @param text $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return text 
     */
    public function getData()
    {
        return $this->data;
    }

    public function getDecodedData(){
        return json_decode($this->data);
    }

    /**
     * Set submitted_at
     *
     * @param datetime $submittedAt
     */
    public function setSubmittedAt($submittedAt)
    {
        $this->submitted_at = $submittedAt;
    }

    /**
     * Get submitted_at
     *
     * @return datetime 
     */
    public function getSubmittedAt()
    {
        return $this->submitted_at;
    }

    /**
     * Set finalized
     *
     * @param boolean $finalized
     */
    public function setFinalized($finalized)
    {
        $this->finalized = $finalized;
    }

    /**
     * Get finalized
     *
     * @return boolean 
     */
    public function getFinalized()
    {
        return $this->finalized;
    }

    /**
     * Set category
     *
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user_id
     *
     * @param Eotvos\VersenyrBundle\Entity\User $userId
     */
    public function setUserId(\Eotvos\VersenyrBundle\Entity\User $userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get user_id
     *
     * @return Eotvos\VersenyrBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set round_id
     *
     * @param Eotvos\VersenyrBundle\Entity\Round $roundId
     */
    public function setRoundId(\Eotvos\VersenyrBundle\Entity\Round $roundId)
    {
        $this->round_id = $roundId;
    }

    /**
     * Get round_id
     *
     * @return Eotvos\VersenyrBundle\Entity\Round 
     */
    public function getRoundId()
    {
        return $this->round_id;
    }
}
