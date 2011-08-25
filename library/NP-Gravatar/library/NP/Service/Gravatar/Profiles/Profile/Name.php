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
 * User's real name data.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile_Name extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Given name.
     *
     * @var string
     */
    protected $_givenName;

    /**
     * Family name.
     *
     * @var string
     */
    protected $_familyName;

    /**
     * Full name.
     *
     * @var string
     */
    protected $_formatted;

    /**
     * Sets $_givenName.
     *
     * @param string $givenName
     * @return NP_Service_Gravatar_Profiles_Profile_Name
     */
    public function setGivenName($givenName)
    {
        $this->_givenName = (string)$givenName;
        
        return $this;
    }

    /**
     * Gets $_givenName.
     *
     * @return string
     */
    public function getGivenName()
    {
        return $this->_givenName;
    }

    /**
     * Sets $_familyName.
     *
     * @param string $familyName
     * @return NP_Service_Gravatar_Profiles_Profile_Name
     */
    public function setFamilyName($familyName)
    {
        $this->_familyName = (string)$familyName;

        return $this;
    }

    /**
     * Gets $_familyName.
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->_familyName;
    }

    /**
     * Sets $_formatted.
     *
     * @param string $formatted
     * @return NP_Service_Gravatar_Profiles_Profile_Name
     */
    public function setFormatted($formatted)
    {
        $this->_formatted = (string)$formatted;

        return $this;
    }

    /**
     * Gets $_formatted.
     *
     * @return string
     */
    public function getFormatted()
    {
        return $this->_formatted;
    }
}