<?php

namespace Cancellar\TrackerBundle\Listener;

use Cancellar\TrackerBundle\Logger\TrackerLoggerInterface;
use Cancellar\CommonBundle\Generator\UuidGenerator;
use Cancellar\CommonBundle\Client\ClientUtils;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class for tracking the user based on a cookie or session
 *
 * @author Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @since 0.1
 * @version 0.1
 *
 * @see LogEntry
 * @see LogEntryRepository
 */
class CookieListener
{
  private $logger;  // logger intance

  private $info;    // additional short information

  private $cookieName; // cookie name

  /**
   * Constructor.
   *
   * Sets the logger class used by the tracker.
   *
   * @param TrackerLoggerInterface $logger
   */
  public function __construct(TrackerLoggerInterface $logger, $cookieName='trackingid'){
    $this->info = null;
    $this->cookieName = $cookieName;
    $this->logger = $logger;
  }

  /**
   * Adds additional info to the log. Should be short, simple tagging.
   *
   * @param string $info short additional information
   */
  public function setRequestInfo($info){
    $this->info = $info;
  }

  /**
   * Handles response parameters
   *
   * @param FilterResponseEvent $event
   */
  public function onResponse(FilterResponseEvent $event){
    if($this->logger === null){
      throw new \Exception('Tracker error: Logger isn\'t set!');
    }
    $request = $event->getRequest();
    $response = $event->getResponse();
    $inSession = false; // found in session
    $inCookie = false;  // found in cookie
    $tokenDiff = false; // found both, but different


    $clientUtils = new ClientUtils();
    $ip = $clientUtils->getRealIp();
    $hostname = $clientUtils->getHostname();
    $useragent = $clientUtils->getUseragentString();

    $reqCookies = $request->cookies;

    $cookieParam = $reqCookies->get($this->cookieName,null);
    $sessionParam = $request->getSession()->get('trackingid', null);

    $inCookie = ($cookieParam !== null);
    $inSession = ($sessionParam !== null);

    if($cookieParam && !$sessionParam){
      $request->getSession()->set('trackingid', $cookieParam);
      $sessionParam = $cookieParam;
    }
    if($sessionParam && !$cookieParam){
      $farFuture = new \DateTime();
      $farFuture->add(new \DateInterval('P30Y')); // 30 years, should be more than enough
      $response->headers->setCookie(new Cookie($this->cookieName, $sessionParam, $farFuture ));
      $cookieParam = $sessionParam;
    }
    if(!$cookieParam){
      $uuidGenerator = new UuidGenerator();
      $cookieParam = $uuidGenerator->generate(UuidGenerator::UUID_TIME, UuidGenerator::FMT_STRING, "domaisdasdn.hu");
      // generate uuid
      // set it
      $farFuture = new \DateTime();
      $farFuture->add(new \DateInterval('P30Y')); // 30 years, should be more than enough
      $response->headers->setCookie(new Cookie($this->cookieName, $cookieParam, $farFuture ));
      $request->getSession()->set('trackingid', $cookieParam);
      $sessionParam = $cookieParam;
    }
    if($cookieParam !== $sessionParam){
      // WTF ? something is cheating stupidly ? anyway, report and leave it as is
      $tokenDiff = true;
    }
    $this->logger->log($cookieParam, $inCookie, $inSession, $ip, $hostname, new \DateTime(), $useragent, ($tokenDiff ? $sessionParam : null), $this->info );

  }
}

