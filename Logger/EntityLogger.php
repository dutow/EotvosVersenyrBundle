<?php

namespace Cancellar\TrackerBundle\Logger;

use Doctrine\ORM\EntityManager;

use Cancellar\TrackerBundle\Entity\LogEntry;
use Cancellar\TrackerBundle\Entity\LogEntryRepository;

/**
 * Logger class logging into a database managed by a doctrine2 entity
 *
 * @author Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @since 0.1
 * @version 0.1
 *
 * @see LogEntry
 * @see LogEntryRepository
 */
class EntityLogger implements TrackerLoggerInterface
{
    private $em;    /// Reference to the Doctrine::EntityManager


    /**
     * Constructor.
     *
     * Sets the entity manager to be used by the logging process.
     *
     * @param EntityManager $em The Entity Manager
     */
    public function __construct(EntityManager $em)
    {
      $this->em = $em;
    }


    /**
     * Generates a new log entry with the given parameters.
     *
     * sessUuid should be set if tracker id found in both cookie and session but differs.
     *
     * @param string $uuid An unique string identifying the client
     * @param bool $inCookie Found identifier in cookie
     * @param bool $inSession Found identifier in session
     * @param string $ip IP address of the client
     * @param string $hostname Hostname address of the client
     * @param datetime $date request time
     * @param string $userAgent useragent string of the request
     * @param string $sessUuid session uuid, if uuid in session and cookie differs
     * @param string infoTag optional string describing the category of the visit
     *
     */
    public function log($uuid, $inCookie, $inSession, $ip, $hostname, \DateTime $date, $userAgent, $sessUuid, $infoTag=null){
      $logEntry = new LogEntry();
      $logEntry->setUuid($uuid);
      $logEntry->setInCookie($inCookie);
      $logEntry->setInSession($inSession);
      $logEntry->setIp($ip);
      $logEntry->setHostname($ip);
      $logEntry->setDate($date);
      $logEntry->setUserAgent($userAgent);
      $logEntry->setSessUuid($sessUuid);
      $this->em->persist($logEntry);
      $this->em->flush();
    }


    /**
     * Loads all log entries for the given uuid.
     *
     * List is generated by uuid and sessUuid fields.
     *
     * @param string $uuid
     *
     * @returns array
     */
    public function getInfoForUuid($uuid){
      return $this->em->getRepository('CancellarTrackerBundle:LogEntry')->findByUuid($uuid);
    }


    /**
     * Loads all log entries for the given ip.
     *
     * List is generated by the client's IP address
     *
     * @param string $ip
     *
     * @returns array
     */
    public function getInfoForIp($ip){
      return $this->em->getRepository('CancellarTrackerBundle:LogEntry')->findByIp($ip);
    }
}
