<?php

class Application_Registry extends Zend_Registry {
    
    /**
     * @return PDO
     */
    public static function getDb() {
        return Zend_Registry::get('db');
    }
    
    /**
     * @return Application_Model_Event_EventRepository 
     */
    public static function getEventRepository() {
        $registry = Zend_Registry::getInstance();
        if ($registry->isRegistered('eventRepository')) {
            return $registry->get('eventRepository');
        } else {
            $searchIndex = Zend_Registry::get('searchIndex');
            $eventRepository = new Application_Model_Event_EventRepository($searchIndex);
            $registry->set('eventRepository', $eventRepository);
            return $eventRepository;
        }
    }
    
    /**
     * @return Application_Model_User_UserRepository 
     */
    public static function getUserRepostiory() {
        $registry = Zend_Registry::getInstance();
        if ($registry->isRegistered('userRepository')) {
            return $registry->get('userRepository');
        } else {
            $searchIndex = Zend_Registry::get('searchIndex');
            $userRepository = new Application_Model_User_UserRepository($searchIndex);
            $registry->set('userRepository', $userRepository);
            return $userRepository;
        }
    }
    
    /**
     * @return Application_Model_User_User
     */
    public static function getCurrentUser() {
        $user = null;
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $auth = Zend_Auth::getInstance();
            $userId = $auth->getIdentity();
            $userRepository = self::getUserRepostiory();
            $user = $userRepository->getOneById($userId);
        }
        if ($user == null) {
            $user = new Application_Model_User_GuestUser();
        }
        return $user;
    }
}
