<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

require_once 'NP/Service/Gravatar/Profiles/Profile/Abstract.php';

/**
 * As profile parts containing "type" and "value" properties is
 * common, this abstract class provides getters and setters for
 * those fields.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
abstract class NP_Service_Gravatar_Profiles_Profile_TypeValue extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Some type.
     *
     * @var string
     */
    protected $_type;

    /**
     * Some value.
     *
     * @var string
     */
    protected $_value;

    /**
     * Sets $_type.
     *
     * @param string $type
     * @return NP_Service_Gravatar_Profiles_Profile_TypeValue
     */
    public function setType($type)
    {
        $this->_type = (string)$type;
        
        return $this;
    }

    /**
     * Gets $_type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Sets $_value.
     *
     * @param string $value
     * @return NP_Service_Gravatar_Profiles_Profile_TypeValue
     */
    public function setValue($value)
    {
        $this->_value = (string)$value;

        return $this;
    }

    /**
     * Gets $_value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }
}