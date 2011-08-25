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
 * Profile background info.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile_Background extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Background color (hex).
     *
     * @var string
     */
    protected $_color;

    /**
     * Background position.
     *
     * @var string
     */
    protected $_position;

    /**
     * Background repeat.
     *
     * @var string
     */
    protected $_repeat;

    /**
     * Background image url.
     *
     * @var Zend_Uri_Http
     */
    protected $_url;

    /**
     * Sets $_color.
     *
     * @param string $color
     * @return NP_Service_Gravatar_Profiles_Profile_Background
     */
    public function setColor($color)
    {
        $this->_color = (string)$color;
        
        return $this;
    }

    /**
     * Gets $_color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->_color;
    }

    /**
     * Sets $_position.
     *
     * @param string $position
     * @return NP_Service_Gravatar_Profiles_Profile_Background
     */
    public function setPosition($position)
    {
        $this->_position = (string)$position;

        return $this;
    }

    /**
     * Gets $_position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * Sets $_repeat.
     *
     * @param string $repeat
     * @return NP_Service_Gravatar_Profiles_Profile_Background
     */
    public function setRepeat($repeat)
    {
        $this->_repeat = (string)$repeat;

        return $this;
    }

    /**
     * Gets $_repeat.
     *
     * @return string
     */
    public function getRepeat()
    {
        return $this->_repeat;
    }

    /**
     * Sets $_url.
     *
     * @param string|Zend_Uri_Http $url
     * @return NP_Service_Gravatar_Profiles_Profile_Background
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
}