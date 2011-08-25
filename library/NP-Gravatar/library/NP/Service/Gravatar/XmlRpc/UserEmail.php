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
 * Represents user's email address data.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_XmlRpc_UserEmail
{
    /**
     * Email address.
     *
     * @var string
     */
    protected $_email;

    /**
     * Image assigned to $_emailAddress.
     *
     * @var Zend_Uri_Http
     */
    protected $_image;

    /**
     * Constructor.
     *
     * @param string $email
     * @param NP_Service_Gravatar_XmlRpc_UserImage|array $image
     */
    public function __construct($email, $image)
    {
        $this->setEmail($email);
        $this->setImage($image);
    }

    /**
     * Sets $_email.
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->_email = (string)$email;
    }

    /**
     * Gets $_email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets $_image.
     *
     * @param NP_Service_Gravatar_XmlRpc_UserImage|array $image
     * @return void
     */
    public function setImage($image)
    {
        if ($image instanceof NP_Service_Gravatar_XmlRpc_UserImage) {
            $this->_image = $image;
        }
        elseif (is_array($image) && isset($image['id']) && isset($image['url']) && isset($image['rating'])) {
            require_once 'NP/Service/Gravatar/XmlRpc/UserImage.php';
            $this->_image = new NP_Service_Gravatar_XmlRpc_UserImage($image['id'], $image['url'], $image['rating']);
        }
        else {
            require_once 'NP/Service/Gravatar/XmlRpc/Exception.php';
            throw new NP_Service_Gravatar_XmlRpc_Exception('Invalid image data is supplied.');
        }
    }

    /**
     * Gets $_image.
     *
     * @return NP_Service_Gravatar_XmlRpc_UserImage
     */
    public function getImage()
    {
        return $this->_image;
    }
}