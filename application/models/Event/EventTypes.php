<?php

class Application_Model_Event_EventTypes 
{
    private $choices;
    
    public function __construct() {
        $this->choices = array(
            'conference' => 'Conference',
            'grant-deadline' => 'Grant Deadline',
            'talk' => 'Talk',
            'training-course' => 'Training Course',
            'workshop' => 'Workshop',
            'other' => 'Other',
        );
    }
    
    public function getChoices() {
        return $this->choices;
    }
    
    public function getLabel($type) {
        $choices = $this->getChoices();
        if (isset($choices[$type])) {
            return $choices[$type];
        }
    }
}
