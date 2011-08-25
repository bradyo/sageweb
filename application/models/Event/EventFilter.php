<?php

class Application_Model_Event_EventFilter extends Application_Model_Post_PostFilter {
    
    protected $eventTypes;
    protected $startDateAfter;
    protected $startDateBefore;
    
    public function __construct() {
        $this->eventTypes = array();
        $this->orderBy = 'event.startDate asc';
    }
    
    public function getSortByOptions() {
        return array(
            'startDate' => 'Start Date',
        );
    }
}
