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
        return null;
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
     * Reorders the given array of [ user, [ category => point ] ] into a descending list.
     * 
     * @param mixed $standing unordered user list
     * 
     * @return array ordered standing
     */
    public function orderStanding($standing)
    {
        $standing2 =array();
        foreach ($standing as $k => $v) {
            $standing2[] = array($k, array_sum($v));
        }

        return $standing2;
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

