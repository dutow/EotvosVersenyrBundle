<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * Return the list of the contensants : not including admins and testers
     * 
     * @return array
     */
    public function getAllContensants()
    {
        return $this
            ->getEntityManager()
            ->createQuery('SELECT u FROM Eotvos\VersenyrBundle\Entity\User u WHERE u.admin=0 AND u.tester=0')->getResult();
    }

    public function getActiveForRound($round)
    {
        $term = $round->getTerm();

        $sectionUsers = $this
            ->getEntityManager()
            ->createQuery('SELECT u FROM Eotvos\VersenyrBundle\Entity\User u JOIN u.registrations r JOIN r.term t WHERE t.id= :termid') //AND u.admin=0 AND u.tester=0')
            ->setParameter('termid', $term->getId())
            ->getResult()
            ;

        if ($round->isFirst()) {
            return $sectionUsers;
        } else {
            // evaluate users who may be still active
            die();
        }
    }
}
