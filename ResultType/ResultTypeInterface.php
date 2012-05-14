<?php

namespace Eotvos\VersenyrBundle\ResultType;

/**
 * Interface for user registration providers. 
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
interface ResultTypeInterface
{
    /**
     * Returns the HTML code of the result table or an error message.
     * 
     * @return string html code
     */
    public function generateHtml($round, $standing);

    /**
     * getName
     * 
     * @return string name of the type
     */
    public function getName();
}





