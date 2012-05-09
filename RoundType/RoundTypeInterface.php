<?php

namespace Eotvos\VersenyrBundle\RoundType;

/**
 * Interface for roundtype providers. 
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
interface RoundTypeInterface
{
    /**
     * Returns the list of the links displayed on the right.
     *
     * @return array of links (url => display name)
     */
    public function getRoundLinks();

    /**
     * Returns the name of the action added to section boxes.
     * 
     * @return string action name
     */
    public function getDescriptionAction();

    /**
     * Returns an URL for viewing a specific submission
     *
     * @param Submission $submission object
     * 
     * @return string URL
     */
    public function getAdminUrlForSubmission($submission);

    /**
     * Returns an URL for configuring a given round
     *
     * @param Round $round object
     * 
     * @return string URL
     */
    public function getConfigurationUrl($round);

    /**
     * Reorders the given array of [ user, [ category => point ] ] into a descending list.
     * 
     * @param mixed $standing unordered user list
     * 
     * @return array ordered standing
     */
    public function orderStanding($standing);

    /**
     * Returns a user friendly name of the type
     * 
     * @return string
     */
    public function getDisplayName();

}








