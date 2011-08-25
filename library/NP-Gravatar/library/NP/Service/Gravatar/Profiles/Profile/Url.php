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
 * Personal link.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile_Url extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Link's title.
     *
     * @var string
     */
    protected $_title;

    /**
     * URL of the link.
     *
     * @var Zend_Uri_Http
     */
    protected $_value;

    /**
     * Sets $_title.
     *
     * @param string $title
     * @return NP_Service_Gravatar_Profiles_Profile_Url
     */
    public function setTitle($title)
    {
        $this->_title = (string)$title;
        
        return $this;
    }

    /**
     * Gets $_title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Sets $_value.
     *
     * @param string|Zend_Uri_Http $value
     * @return NP_Service_Gravatar_Profiles_Profile_Url
     */
    public function setValue($value)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_value = NP_Service_Gravatar_Utility::normalizeUri($value);

        return $this;
    }

    /**
     * Gets $_value.
     *
     * @return Zend_Uri_Http
     */
    public function getValue()
    {
        return $this->_value;
    }
}