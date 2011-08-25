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
 * User's email address.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile_Email extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Primary email?
     *
     * @var bool
     */
    protected $_primary = false;

    /**
     * Email address.
     *
     * @var string
     */
    protected $_value;

    /**
     * Sets $_primary.
     *
     * @param string|bool $primary
     * @return NP_Service_Gravatar_Profiles_Profile_Email
     */
    public function setPrimary($primary)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_primary = NP_Service_Gravatar_Utility::normalizeBool($primary);
        
        return $this;
    }

    /**
     * Gets $_primary.
     *
     * @return bool
     */
    public function getPrimary()
    {
        return $this->_primary;
    }

    /**
     * Sets $_value.
     *
     * @param string $value
     * @return NP_Service_Gravatar_Profiles_Profile_Email
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