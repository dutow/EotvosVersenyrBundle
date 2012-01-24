<?php

namespace Cancellar\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cancellar\TrackerBundle\Entity\LogEntry
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Cancellar\TrackerBundle\Entity\LogEntryRepository")
 */
class LogEntry implements TrackerEntityInterface
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
     * @var string $uuid
     *
     * @ORM\Column(name="uuid", type="string", length=36)
     */
    private $uuid;

    /**
     * @var boolean $in_cookie
     *
     * @ORM\Column(name="in_cookie", type="boolean")
     */
    private $in_cookie;

    /**
     * @var boolean $in_session
     *
     * @ORM\Column(name="in_session", type="boolean")
     */
    private $in_session;

    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=16)
     */
    private $ip;

    /**
     * @var string $hostname
     *
     * @ORM\Column(name="hostname", type="string", length=128)
     */
    private $hostname;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string $user_agent
     *
     * @ORM\Column(name="user_agent", type="string", length=128)
     */
    private $user_agent;

    /**
     * @var string $sess_uuid
     *
     * @ORM\Column(name="sess_uuid", type="string", length=36)
     */
    private $sess_uuid;


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
     * Set uuid
     *
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set in_cookie
     *
     * @param boolean $inCookie
     */
    public function setInCookie($inCookie)
    {
        $this->in_cookie = $inCookie;
    }

    /**
     * Get in_cookie
     *
     * @return boolean 
     */
    public function getInCookie()
    {
        return $this->in_cookie;
    }

    /**
     * Set in_session
     *
     * @param boolean $inSession
     */
    public function setInSession($inSession)
    {
        $this->in_session = $inSession;
    }

    /**
     * Get in_session
     *
     * @return boolean 
     */
    public function getInSession()
    {
        return $this->in_session;
    }

    /**
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set hostname
     *
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user_agent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->user_agent = $userAgent;
    }

    /**
     * Get user_agent
     *
     * @return string 
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * Set sess_uuid
     *
     * @param string $sessUuid
     */
    public function setSessUuid($sessUuid)
    {
        $this->sess_uuid = $sessUuid;
    }

    /**
     * Get sess_uuid
     *
     * @return string 
     */
    public function getSessUuid()
    {
        return $this->sess_uuid;
    }
}
