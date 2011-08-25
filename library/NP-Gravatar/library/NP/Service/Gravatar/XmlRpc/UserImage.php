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
 * Represents user's image.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_XmlRpc_UserImage
{
    /**
     * Image id.
     *
     * @var string
     */
    protected $_id;

    /**
     * Image url.
     *
     * @var Zend_Uri_Http
     */
    protected $_url;

    /**
     * Image rating.
     *
     * @var string
     */
    protected $_rating;

    /**
     * Constructor.
     *
     * @param string $id
     * @param string $url
     * @param int|string $rating
     */
    public function __construct($id, $url, $rating)
    {
        $this->setId($id);
        $this->setUrl($url);
        $this->setRating($rating);
    }

    /**
     * Sets $_id.
     *
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->_id = (string)$id;
    }

    /**
     * Gets $_id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets $_url.
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->_url = NP_Service_Gravatar_Utility::normalizeUri($url);
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
     * Sets $_rating.
     *
     * @param int|string $rating
     * @return void
     */
    public function setRating($rating)
    {
        $validRatings = NP_Service_Gravatar_XmlRpc::getValidRatings();
        if (array_key_exists($rating, $validRatings)) {
            $this->_rating = $rating;
        }
        elseif (in_array($rating, $validRatings)) {
            $this->_rating = array_search($rating, $validRatings);
        }
        else {
            require_once 'NP/Service/Gravatar/XmlRpc/Exception.php';
            throw new NP_Service_Gravatar_XmlRpc_Exception('Invalid rating.');
        }
    }

    /**
     * Gets $_rating.
     *
     * @return string
     */
    public function getRating()
    {
        return $this->_rating;
    }
}