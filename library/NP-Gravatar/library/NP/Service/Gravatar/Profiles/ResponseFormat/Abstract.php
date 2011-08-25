<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

/**
 * Abstract response format.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
{
    /**
     * Represents id of a response format. Must be implemented by
     * the extending classes.
     *
     * @var string
     */
    protected $_id = null;

    /**
     * Constructor.
     *
     * @return void
     */
    public function  __construct()
    {
        if ($this->_id === null) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception('Id of a response format must be set, in order
                to this class behave as adapter for the Gravatar service.');
        }
    }

    /**
     * Gets response format id.
     * 
     * @return string
     */
    public function getResponseFormatId()
    {
        return $this->_id;
    }

    /**
     * __toString() implementation.
     *
     * @return string
     */
    public function  __toString()
    {
        return $this->getResponseFormatId();
    }
}