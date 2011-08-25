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
 * Profile of some Gravatar's user.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile extends NP_Service_Gravatar_Profiles_Profile_Abstract
{
    /**
     * Profile id.
     *
     * @var int
     */
    protected $_id;

    /**
     * Profile url.
     *
     * @var Zend_Uri_Http
     */
    protected $_profileUrl;

    /**
     * Preferred username.
     *
     * @var string
     */
    protected $_preferredUsername;

    /**
     * Thumbnail url.
     *
     * @var Zend_Uri_Http
     */
    protected $_thumbnailUrl;

    /**
     * User's photos.
     *
     * @var array
     */
    protected $_photos;

    /**
     * Profile's background info.
     *
     * @var NP_Service_Gravatar_Profiles_Profile_Background
     */
    protected $_profileBackground = null;

    /**
     * User's real name data.
     *
     * @var NP_Service_Gravatar_Profiles_Profile_Name
     */
    protected $_name = null;
    
    /**
     * Public name.
     *
     * @var string
     */
    protected $_displayName;

    /**
     * Some text about user.
     *
     * @var string
     */
    protected $_aboutMe = null;

    /**
     * User's location.
     *
     * @var string
     */
    protected $_currentLocation = null;

    /**
     * Phone numbers.
     *
     * @var array
     */
    protected $_phoneNumbers = array();

    /**
     * Email addresses.
     *
     * @var array
     */
    protected $_emails = array();

    /**
     * IM accounts.
     *
     * @var array
     */
    protected $_ims = array();

    /**
     * Verified accounts.
     *
     * @var array
     */
    protected $_accounts = array();

    /**
     * Personal links.
     *
     * @var array
     */
    protected $_urls = array();

    /**
     * Sets $_id.
     *
     * @param int $id
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setId($id)
    {
        $this->_id = (int)$id;

        return $this;
    }

    /**
     * Gets $_id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets profile url.
     *
     * @param string|Zend_Uri_Http
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setProfileUrl($url)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_profileUrl = NP_Service_Gravatar_Utility::normalizeUri($url);

        return $this;
    }

    /**
     * Gets profile url.
     *
     * @return Zend_Uri_Http
     */
    public function getProfileUrl()
    {
        return $this->_profileUrl;
    }

    /**
     * Sets $_preferredUsername.
     *
     * @param string $preferredUsername
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setPreferredUsername($preferredUsername)
    {
        $this->_preferredUsername = (string)$preferredUsername;

        return $this;
    }

    /**
     * Gets $_preferredUsername.
     *
     * @return string
     */
    public function getPreferredUsername()
    {
        return $this->_preferredUsername;
    }

    /**
     * Sets $_thumbnailUrl.
     *
     * @param string|Zend_Uri_Http $thumbnailUrl
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_thumbnailUrl = NP_Service_Gravatar_Utility::normalizeUri($thumbnailUrl);

        return $this;
    }

    /**
     * Gets $_thumbnailUrl.
     *
     * @return Zend_Uri_Http
     */
    public function getThumbnailUrl()
    {
        return $this->_thumbnailUrl;
    }

    /**
     * Sets $_photos.
     *
     * @param array $photos
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setPhotos(array $photos)
    {
        $this->_photos = NP_Service_Gravatar_Utility::normalizeArrayPropertyValue('Photo', $photos);

        return $this;
    }

    /**
     * Gets $_photos.
     *
     * @return array
     */
    public function getPhotos()
    {
        return $this->_photos;
    }

    /**
     * Sets $_profileBackground.
     *
     * @param NP_Service_Gravatar_Profiles_Profile_Background|array $background
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setProfileBackground($background)
    {
        $this->_profileBackground = NP_Service_Gravatar_Utility::normalizePropertyValue('Background', $background);

        return $this;
    }

    /**
     * Gets $_profileBackground.
     *
     * @return NP_Service_Gravatar_Profiles_Profile_Background
     */
    public function getProfileBackground()
    {
        return $this->_profileBackground;
    }

    /**
     * Sets $_name.
     *
     * @param NP_Service_Gravatar_Profiles_Profile_Name|array $name
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setName($name)
    {
        $this->_name = NP_Service_Gravatar_Utility::normalizePropertyValue('Name', $name);
        
        return $this;
    }

    /**
     * Gets $_name.
     *
     * @return NP_Service_Gravatar_Profiles_Profile_Name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets $_displayName.
     *
     * @param string $displayName
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setDisplayName($displayName)
    {
        $this->_displayName = (string)$displayName;

        return $this;
    }

    /**
     * Gets $_displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->_displayName;
    }

    /**
     * Sets $_aboutMe.
     *
     * @param string $aboutMe
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setAboutMe($aboutMe)
    {
        $this->_aboutMe = (string)$aboutMe;

        return $this;
    }

    /**
     * Gets $_aboutMe.
     *
     * @return string
     */
    public function getAboutMe()
    {
        return $this->_aboutMe;
    }

    /**
     * Sets $_currentLocation.
     *
     * @param string $currentLocation
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setCurrentLocation($currentLocation)
    {
        $this->_currentLocation = (string)$currentLocation;

        return $this;
    }

    /**
     * Gets $_currentLocation.
     *
     * @return string
     */
    public function getCurrentLocation()
    {
        return $this->_currentLocation;
    }

    /**
     * Sets $_phoneNumbers.
     *
     * @param array $phoneNumbers
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setPhoneNumbers(array $phoneNumbers)
    {
        $this->_phoneNumbers = NP_Service_Gravatar_Utility::normalizeArrayPropertyValue('PhoneNumber', $phoneNumbers);

        return $this;
    }

    /**
     * Gets $_phoneNumbers.
     *
     * @return array
     */
    public function getPhoneNumbers()
    {
        return $this->_phoneNumbers;
    }

    /**
     * Sets $_emails.
     *
     * @param array $emails
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setEmails(array $emails)
    {
        $this->_emails = NP_Service_Gravatar_Utility::normalizeArrayPropertyValue('Email', $emails);

        return $this;
    }

    /**
     * Gets $_emails.
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->_emails;
    }

    /**
     * Sets $_ims.
     *
     * @param array $ims
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setIms(array $ims)
    {
        $this->_ims = NP_Service_Gravatar_Utility::normalizeArrayPropertyValue('Im', $ims);

        return $this;
    }

    /**
     * Gets $_ims.
     *
     * @return array
     */
    public function getIms()
    {
        return $this->_ims;
    }

    /**
     * Sets $_accounts.
     *
     * @param array $accounts
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setAccounts(array $accounts)
    {
        $this->_accounts = NP_Service_Gravatar_Utility::normalizeArrayPropertyValue('Account', $accounts);

        return $this;
    }

    /**
     * Gets $_ims.
     *
     * @return array
     */
    public function getAccounts()
    {
        return $this->_accounts;
    }

    /**
     * Sets $_urls.
     *
     * @param array $iurls
     * @return NP_Service_Gravatar_Profiles_Profile
     */
    public function setUrls(array $urls)
    {
        $this->_urls = NP_Service_Gravatar_Utility::normalizeArrayPropertyValue('Url', $urls);

        return $this;
    }

    /**
     * Gets $_ims.
     *
     * @return array
     */
    public function getUrls()
    {
        return $this->_urls;
    }
}