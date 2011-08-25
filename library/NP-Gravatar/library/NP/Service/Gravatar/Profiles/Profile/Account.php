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
 * Verified account.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile_Account extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Domain.
     *
     * @var string
     */
    protected $_domain;

    /**
     * Account username.
     *
     * @var string
     */
    protected $_username;

    /**
     * Display name.
     *
     * @var string
     */
    protected $_display;

     /**
     * Account url.
     *
     * @var Zend_Uri_Http
     */
    protected $_url;

    /**
     * Verified?
     *
     * @var bool
     */
    protected $_verified = false;

    /**
     * Short name of the account.
     *
     * @var string
     */
    protected $_shortname = null;

    /**
     * Sets $_domain.
     *
     * @param string $domain
     * @return NP_Service_Gravatar_Profiles_Profile_Account
     */
    public function setDomain($domain)
    {
        $this->_domain = (string)$domain;
        
        return $this;
    }

    /**
     * Gets $_domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Sets $_username.
     *
     * @param string $username
     * @return NP_Service_Gravatar_Profiles_Profile_Account
     */
    public function setUsername($username)
    {
        $this->_username = (string)$username;

        return $this;
    }

    /**
     * Gets $_username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets $_display.
     *
     * @param string $display
     * @return NP_Service_Gravatar_Profiles_Profile_Account
     */
    public function setDisplay($display)
    {
        $this->_display = (string)$display;

        return $this;
    }

    /**
     * Gets $_display.
     *
     * @return string
     */
    public function getDisplay()
    {
        return $this->_display;
    }

     /**
     * Sets $_url.
     *
     * @param string|Zend_Uri_Http $url
     * @return NP_Service_Gravatar_Profiles_Profile_Account
     */
    public function setUrl($url)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_url = NP_Service_Gravatar_Utility::normalizeUri($url);

        return $this;
    }

    /**
     * Gets $_url.
     *
     * @return Zend_Uri_Http
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Sets $_verified.
     *
     * @param string|bool $verified
     * @return NP_Service_Gravatar_Profiles_Profile_Account
     */
    public function setVerified($verified)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_verified = NP_Service_Gravatar_Utility::normalizeBool($verified);

        return $this;
    }

    /**
     * Gets $_verified.
     *
     * @return bool
     */
    public function getVerified()
    {
        return $this->_verified;
    }
    
    /**
     * Sets $_shortname.
     *
     * @param string $shortname
     * @return NP_Service_Gravatar_Profiles_Profile_Account
     */
    public function setShortname($shortname)
    {
        $this->_shortname = (string)$shortname;

        return $this;
    }

    /**
     * Gets $_shortname.
     *
     * @return string
     */
    public function getShortname()
    {
        return $this->_shortname;
    }
}