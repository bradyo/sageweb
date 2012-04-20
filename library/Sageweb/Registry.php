<?php

/**
 * Wrapper for the Zend registry to fetch specific objects.
 *
 * @author brady
 */
class Sageweb_Registry
{
    /**
     * @return Zend_Db_Adapter_Pdo_Abstract
     */
    public static function getDb()
    {
        return Zend_Registry::get('db');
    }

    /**
     * Gets the User object of the currently logged in user
     * @return Sageweb_Cms_User
     */
    public static function getUser()
    {
        $session = new Zend_Session_Namespace('user');

        $user = $session->user;
        if (!$user) {
            $user = Sageweb_Cms_Table_User::getDefaultUser();
            $session->user = $user;
        }
        return $user;
    }

    /**
     * @return Zend_Cache_Core
     */
    public static function getCache()
    {
        return Zend_Registry::get('cache');
    }

    /**
     * @return Zend_Mail
     */
    public static function getMailer()
    {
        return Zend_Registry::get('mailer');
    }

    /**
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public static function getFlashMessenger()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getSiteIndex()
    {
        return Zend_Registry::get('siteIndex');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getContentIndex()
    {
        return Zend_Registry::get('contentIndex');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getEventIndex()
    {
        return Zend_Registry::get('eventIndex');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getDiscussionIndex()
    {
        return Zend_Registry::get('discussionIndex');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getPaperIndex()
    {
        return Zend_Registry::get('paperIndex');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getPersonIndex()
    {
        return Zend_Registry::get('personIndex');
    }

    /**
     * @return Zend_Search_Lucene_Proxy
     */
    public static function getLabIndex()
    {
        return Zend_Registry::get('labIndex');
    }
}
