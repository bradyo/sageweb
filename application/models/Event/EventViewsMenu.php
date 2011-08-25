<?php

class Application_Model_Event_EventViewsMenu extends Zend_Navigation 
{
    public function __construct() {
        $page = new Application_Navigation_Page_MvcCount(array(
            'label' => 'Upcoming',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'events',
            'action' => 'upcoming',
        ));
        $page->setResetParams(false);
        $this->addPage($page);
        
        $page = new Application_Navigation_Page_MvcCount(array(
            'label' => 'Past',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'events',
            'action' => 'past',
        ));
        $page->setResetParams(false);
        $this->addPage($page);
        
        $page = new Application_Navigation_Page_MvcCount(array(
            'label' => 'Calendar',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'events',
            'action' => 'calendar',
        ));
        $page->setResetParams(false);
        $this->addPage($page);
    }
}
