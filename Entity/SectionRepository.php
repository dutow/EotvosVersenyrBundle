<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SectionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SectionRepository extends EntityRepository
{
    /**
     * Returns sections for a term
     * 
     * @param mixed $term target
     * 
     * @return array
     */
    public function getForTerm($term)
    {
        $rm = $this->getEntityManager()->getRepository('\EotvosVersenyrBundle:TextPage');
        $root = $rm->getTermRoot($term);

        return $this->getEntityManager()
            ->createQuery('SELECT s, tp FROM Eotvos\VersenyrBundle\Entity\Section s JOIN s.page tp WHERE tp.root=:root_id ORDER BY tp.lft ASC')
            ->setParameter('root_id', $root->getId())
            ->getResult();
    }

}
