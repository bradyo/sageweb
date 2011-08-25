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
 * Client for the Gravatar's XML-RPC service.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_XmlRpc
{
    const SECURE_XMLRPC_SERVER = 'https://secure.gravatar.com/xmlrpc';

    const G_RATED  = 0;
    const PG_RATED = 1;
    const R_RATED  = 2;
    const X_RATED  = 3;

    /**
     * The API key of the Gravatar account. It can be retrieved on
     * the page for editing profile, on wordpress.com.
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * Email address that corresponds to $_apiKey.
     * 
     * @var string
     */
    protected $_email;

    /**
     * XML-RPC client.
     *
     * @var Zend_XmlRpc_Client
     */
    protected $_xmlRpcClient;

    /**
     * Valid Gravatar ratings.
     *
     * @var array
     */
    protected static $_validRatings = array(
        'G'  => self::G_RATED,
        'PG' => self::PG_RATED,
        'R'  => self::R_RATED,
        'X'  => self::X_RATED
    );

    /**
     * Constructor.
     * 
     * @param string $apiKey
     * @param string $email
     * @return void
     */
    public function  __construct($apiKey, $email)
    {
        $this->setApiKey($apiKey);
        $this->setEmail($email);
    }

    /**
     * Sets API key.
     *
     * @param string $apiKey
     * @return NP_Service_Gravatar_XmlRpc
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = (string)$apiKey;

        return $this;
    }

    /**
     * Retrieves API key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Sets address.
     *
     * @param string $email
     * @return NP_Service_Gravatar_XmlRpc
     */
    public function setEmail($email)
    {
        $this->_email = strtolower(trim((string)$email));

        return $this;
    }

    /**
     * Retrieves email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Retrieves XML-RPC client instance.
     *
     * If the client hasn't being inizialized yet, then a new
     * Zend_XmlRpc_Client instance is created.
     *
     * @return Zend_XmlRpc_Client
     */
    public function getXmlRpcClient()
    {
        if (!$this->_xmlRpcClient) {
            /**
             * @see Zend_XmlRpc_Client
             */
            require_once 'Zend/XmlRpc/Client.php';
            $this->_xmlRpcClient = new Zend_XmlRpc_Client($this->_getServerUri());
        }

        return $this->_xmlRpcClient;
    }

    /**
     * Generates and returns server uri to be used by
     * $_xmlRpcClient.
     * 
     * @return string
     */
    protected function _getServerUri()
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        
        return self::SECURE_XMLRPC_SERVER . '?user=' . NP_Service_Gravatar_Utility::emailHash($this->getEmail());
    }

    /**
     * Gets valid Gravatar ratings.
     *
     * @return array
     */
    public static function getValidRatings()
    {
        return self::$_validRatings;
    }


    /**
     * Checks whether there's a gravatar account registered with
     * supplied $emailAddresses.
     * 
     * @param string|array $emailAddresses
     * @return array
     */
    public function exists($emailAddresses)
    {
        if (!is_array($emailAddresses)) {
            $emailAddresses = array($emailAddresses);
        }

        require_once 'NP/Service/Gravatar/Utility.php';
        //Generating hashes.
        $params = array(
            'hashes'=>array_map(array('NP_Service_Gravatar_Utility', 'emailHash'), $emailAddresses)
        );
        
        return $this->_call('exists', $params);
    }

    /**
     * Gets list of addresses for the current account.
     *
     * @param bool $raw Whether raw response should be returned.
     * @return array
     */
    public function addresses($raw = false)
    {
        $retval = $this->_call('addresses');

        if ($raw) {
            return $retval;
        }
        
        $addresses = array();
        require_once 'NP/Service/Gravatar/XmlRpc/UserEmail.php';
        foreach ($retval as $email=>$data) {
             $addresses[] = new NP_Service_Gravatar_XmlRpc_UserEmail(
                     $email,
                     array(
                         'id'=>$data['userimage'],
                         'url'=>$data['userimage_url'],
                         'rating'=>$data['rating']
                     )
             );
        }

        return $addresses;
    }

    /**
     * Gets list of images for the current account.
     *
     * @param bool $raw Whether raw response should be returned.
     * @return array
     */
    public function userImages($raw = false)
    {
        $retval = $this->_call('userimages');

        if ($raw) {
            return $retval;
        }
        
        $images = array();
        require_once 'NP/Service/Gravatar/XmlRpc/UserImage.php';
        foreach ($retval as $id=>$data) {
             $images[] = new NP_Service_Gravatar_XmlRpc_UserImage($id, $data[1], $data[0]);
        }

        return $images;
    }

    /**
     * Save binary image data as a user image for the current account.
     *
     * @param string $image Path to some image or its content.
     * @package int $rating
     * @return bool|string
     */
    public function saveData($image, $rating = self::G_RATED)
    {
        $image = (string)$image;
        $rating = (int)$rating;
        
        if (!in_array($rating, self::$_validRatings)) {
            require_once 'NP/Service/Gravatar/XmlRpc/Exception.php';
            throw new NP_Service_Gravatar_XmlRpc_Exception('Invalid Gravatar rating is supplied.');
        }

        if (is_file($image) && is_readable($image)) { //Image is file name? Get its content.
            $image = file_get_contents($image);
        }
        
        $params = array(
            'data'=>base64_encode($image),
            'rating'=>$rating
        );

        return $this->_call('saveData', $params);
    }

    /**
     * Read an image via its URL and save that as a userimage for
     * current account.
     *
     * @param string $imageUrl
     * @package int $rating
     * @return bool|string
     */
    public function saveUrl($imageUrl, $rating = self::G_RATED)
    {
        $imageUrl = (string)$imageUrl;
        $rating = (int)$rating;

        if (!in_array($rating, self::$_validRatings)) {
            require_once 'NP/Service/Gravatar/XmlRpc/Exception.php';
            throw new NP_Service_Gravatar_XmlRpc_Exception('Invalid Gravatar rating is supplied.');
        }

        $params = array(
            'url'=>$imageUrl,
            'rating'=>$rating
        );

        return $this->_call('saveUrl', $params);
    }

    /**
     * Selects some image to be used one of more email addresses
     * on current account.
     *
     * @param string $imageId
     * @param string|array $emailAddresses
     * @return array
     */
    public function useUserImage($imageId, $emailAddresses)
    {
        $imageId = (string)$imageId;
        if (!is_array($emailAddresses)) {
            $emailAddresses = array($emailAddresses);
        }

        $params = array(
            'userimage'=>$imageId,
            'addresses'=>$emailAddresses
        );

        return $this->_call('useUserimage', $params);
    }

    /**
     * Removes the image associated with one or more email
     * addresses.
     *
     * @param string|array $emailAddresses
     * @return bool
     */
    public function removeImage($emailAddresses)
    {
        if (!is_array($emailAddresses)) {
            $emailAddresses = array($emailAddresses);
        }

        $params = array(
            'addresses'=>$emailAddresses
        );

        return $this->_call('removeImage', $params);
    }

    /**
     * Removes a userimage from the account and any email
     * addresses with which it is associated.
     *
     * @param string $imageId
     * @return bool
     */
    public function deleteUserImage($imageId)
    {
        $params = array(
            'userimage'=>(string)$imageId
        );

        return $this->_call('deleteUserimage', $params);
    }

    /**
     * A test function.
     *
     * @return mixed
     */
    public function test()
    {
        return $this->_call('test');
    }

    /**
     * Executes XML-RPC request by proxying local XML-RPC client.
     *
     * @see Zend_XmlRpc_Client
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function _call($method, $params = array())
    {
        $params = array_merge($params, array('apikey'=>$this->getApiKey()));
        
        $this->getXmlRpcClient()->setSkipSystemLookup(true); //We will manually prepare params.
        
        try {
            $retval = $this->getXmlRpcClient()->call(
                    'grav.' . $method,
                    array(Zend_XmlRpc_Value::getXmlRpcValue($params, Zend_XmlRpc_Value::XMLRPC_TYPE_STRUCT))
             );
            
            return $retval;
        }
        catch (Zend_XmlRpc_Client_FaultException $ex) {
            require_once 'NP/Service/Gravatar/XmlRpc/Exception.php';
            throw new NP_Service_Gravatar_XmlRpc_Exception($ex->getMessage());
        }
    }
}