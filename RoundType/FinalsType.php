<?php

namespace Eotvos\VersenyrBundle\RoundType;

/**
 * Basic type for offline finals
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class FinalsType
{
    /**
     * Returns the list of the links displayed on the right.
     *
     * @return array of links (url => display name)
     */
    public function getRoundLinks()
    {
        return array();
    }

    /**
     * Returns the name of the action added to section boxes.
     * 
     * @return string action name
     */
    public function getDescriptionAction()
    {
        return 'eotvos.versenyr.round.finals:activeDescriptionAction';
    }

    /**
     * Returns an URL for viewing a specific submission
     *
     * @param Submission $submission object
     * 
     * @return string URL
     */
    public function getAdminUrlForSubmission($submission)
    {
        return null;
    }

    /**
     * Returns an URL for configuring a given round
     *
     * @param Round $round object
     * 
     * @return string URL
     */
    public function getConfigurationUrl($round)
    {
        return null;
    }

    /**
     * Reorders the given array of [ user, [ submissions ], nullpoints ] into a descending list.
     * 
     * @param mixed $standing unordered user list
     * 
     * @return array ordered standing
     */
    public function orderStanding($standing)
    {
        foreach ($standing as $k => $v) {
            $sum = 0;
            foreach ($v[1] as $subm) {
                $sum += $subm->getPoints();
            }

            $standing[$k][2] = $sum;
        }
        $rs = $standing;

        uasort($rs, function($a,$b) {
            return (($a[2]==$b[2]) ? 0 : ($a[2] < $b[2] ? -1 : 1));
        });

        return $rs;
    }

    /**
     * Returns a user friendly name of the type
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return 'FinalsType';
    }

}

