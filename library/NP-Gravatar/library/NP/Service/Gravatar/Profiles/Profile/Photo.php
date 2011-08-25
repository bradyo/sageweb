<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

require_once 'NP/Service/Gravatar/Profiles/Profile/TypeValue.php';

/**
 * Profile photo.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_Profile_Photo extends NP_Service_Gravatar_Profiles_Profile_TypeValue
{
    /**
     * Defined by NP_Service_Gravatar_Profiles_Profile_TypeValue.
     * 
     * Sets $_value.
     *
     * @param string|Zend_Uri_Http $value
     * @return NP_Service_Gravatar_Profiles_Profile_Photo
     */
    public function setValue($value)
    {
        //Making sure that $_value is Zend_Uri_Http instance.
        require_once 'NP/Service/Gravatar/Utility.php';
        $this->_value = NP_Service_Gravatar_Utility::normalizeUri($value);
        
        return $this;
    }
}