<?php

class Application_Model_Event_EventTypes 
{
    private $_choices;
    
    public function __construct() {
        $this->_choices = array(
            'conference' => 'Conference',
            'grant-deadline' => 'Grant Deadline',
            'talk' => 'Talk',
            'training-course' => 'Training Course',
            'workshop' => 'Workshop',
            'other' => 'Other',
        );
    }
    
    public function getChoices() {
        return $this->_choices;
    }
    
    public function getLabel($type) {
        if (isset($this->_choices[$type])) {
            return $this->_choices[$type];
        }
    }
}
