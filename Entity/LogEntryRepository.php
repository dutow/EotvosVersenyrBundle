<?php

namespace Cancellar\TrackerBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * LogEntry related helper repository methods.
 *
 * @author Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @since 0.1
 * @version 0.1
 *
 * @see LogEntry
 */
class LogEntryRepository extends EntityRepository
{
  /**
   * Returns the list of the records related to the uuid
   *
   * @param string $uuid Search base
   * @param integer $hydrationMode Doctrine2 hydration mode
   * @return list of records
   */
  public function findByUuid($uuid, $hydrationMode = Query::HYDRATE_ARRAY){
    return
      $this->createQuery('SELECT * FROM CancellarTrackerBundle:LogEntry le WHERE le.uuid = :uuid OR le.sess_uuid = :uuid')
           ->setParameter('uuid', $uuid)
           ->setHydrationMode($hydrationMode)
           ->execute()
           ;
  }

  /**
   * Returns the list of visitors in a given time frame
   *
   * @todo Refactor MIN ip to LAST ip
   * @todo implement date limits
   * @param string starting date (optional)
   * @param string end date (optional)
   * @param integer $hydrationMode Doctrine2 hydration mode
   * @return list of records
   */
  public function findUniqueVisitors($from=null, $until=null, $hydrationMode = Query::HYDRATE_ARRAY){
    $qb = $this->_em->createQueryBuilder();
    $qb->add('select', 'le.uuid, MIN(le.date) AS first, MAX(le.date) AS last, MIN(le.ip) AS ip');
    $qb->add('from', 'CancellarTrackerBundle:LogEntry le');
    $qb->add('groupBy', 'le.uuid');
    $qb->add('orderBy', 'first ASC');
    return $this->_em->createQuery($qb->getDql())->setHydrationMode($hydrationMode)->execute();
  }
}

