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
 * Collection of utilities that are used in various
 * NP_Service_Gravatar classes.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Utility
{
    /**
     * Plugin loader for profile classes.
     * 
     * @var Zend_Loader_PluginLoader 
     */
    protected static $_pluginLoader;

    /**
     * Gets profile classes plugin loader.
     *
     * @return Zend_Loader_PluginLoader
     */
    public static function getPluginLoader()
    {
        if (!self::$_pluginLoader) {
            require_once 'Zend/Loader/PluginLoader.php';
            self::$_pluginLoader = new Zend_Loader_PluginLoader(array(
                'NP_Service_Gravatar_Profiles_Profile_'=>'NP/Service/Gravatar/Profiles/Profile/')
            );
        }

        return self::$_pluginLoader;
    }

    /**
     * Generates email hash.
     *
     * @param string $email Email address.
     * @return string
     */
    public static function emailHash($email)
    {
        return md5((string)$email);
    }
    
    /**
     * Generates and returns value for some array-type property in some
     * of Profile classes, based on supplied $data array, and creates
     * $profileClass instances if necessary, so that value of $property
     * is collection of $profileClass instances.
     *
     * @param string $profileClass Name of the profile class.
     * @param array $data Data that needs to be set for $property.
     * @return array|null
     */
    public static function normalizeArrayPropertyValue($profileClass, array $data)
    {
        $value = array();

        $current = current($data);
        $className = '';
        try {
            $className = self::getPluginLoader()->load($profileClass);
        }
        catch (Zend_Loader_PluginLoader_Exception $e) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception($e->getMessage());
        }
        
        if (!is_array($current) && !$current instanceof $className) {
            $data = array($data);
        }

        foreach ($data as $val) {
            if ($profileInstance = self::normalizePropertyValue($profileClass, $val)) {
                $value[] = $profileInstance;
            }
        }

        //Valid value? Return it. Otherwise, return null.
        return (!empty($value)) ? $value : null;
    }

    /**
     * Generates and returns value for some property that holds instance
     * of some Profile class.
     *
     * @param string $profileClass
     * @param mixed $value
     * @return NP_Service_Gravatar_Profiles_Profile_Abstract|null
     */
    public static function normalizePropertyValue($profileClass, $value)
    {
        try {
            $className = self::getPluginLoader()->load($profileClass);
        }
        catch (Zend_Loader_PluginLoader_Exception $e) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception($e->getMessage());
        }

        if ($value instanceof $className) { //Already instantiated?
            return $value;
        }
        elseif (is_array($value)) { //Array? Generate profile instance.
            return new $className($value);
        }

        return null;
    }

    /**
     * Parses, validates and returns a valid Zend_Uri object
     * from given $value.
     *
     * @param string|Zend_Uri_Http $value
     * @return Zend_Uri_Http
     */
    public static function normalizeUri($value)
    {
        require_once 'Zend/Uri.php';
        if ($value instanceof Zend_Uri_Http) {
            $uri = $value;
        } else {
            try {
                $uri = Zend_Uri::factory((string)$value);
            }
            catch (Exception $e) {
                require_once 'NP/Service/Gravatar/Exception.php';
                throw new NP_Service_Gravatar_Exception($e->getMessage());
            }
        }

        //Allow only Zend_Uri_Http objects.
        if (!($uri instanceof Zend_Uri_Http)) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception("Invalid URL $uri.");
        }

        return $uri;
    }

    /**
     * Normalizes bool values - converts strings containing
     * "true" to boolean true.
     *
     * @param mixed $value
     * @return bool
     */
    public static function normalizeBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return (preg_match('/true/i', (string)$value) === 1) ? true : false;
    }
}