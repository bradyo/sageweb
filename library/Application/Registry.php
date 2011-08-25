<?php

class Application_Registry extends Zend_Registry {
    
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
    
}
