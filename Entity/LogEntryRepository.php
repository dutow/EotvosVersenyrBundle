<?php

namespace Cancellar\TrackerBundle\Entity;

use Doctrine\ORM\EntityRepository;

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
  public function findByUuid($uuid, $hydrationMode = Doctrine::HYDRATE_ARRAY){
    return
      $this->createQuery('SELECT * FROM CancellarTrackerBundle:LogEntry le WHERE le.uuid = :uuid OR le.sess_uuid = :uuid')
           ->setParameter('uuid', $uuid)
           ->setHydrationMode($hydrationMode)
           ->execute()
           ;
  }
}

