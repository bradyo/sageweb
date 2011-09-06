<?php

class Application_Model_Event_EventForm extends Application_Model_Post_PostForm {
    
    private $eventTypeChoices;
    
    public function __construct(Application_Model_User_User $viewingUser, $options = array()) {
        parent::__construct($viewingUser, $options);
        
        echo "initing EventForm";

        
        $eventTypes = new Application_Model_Event_EventTypes();
        $this->eventTypeChoices = $eventTypes.getChoices();

        $element = new Zend_Form_Element_Select('eventType');
        $element->setLabel('Event Type:');
        $options = $this->eventTypeChoices;
        $element->setMultiOptions($options);
        $element->addValidator(new Zend_Validate_InArray(array_keys($options)));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('location');
        $element->setLabel('Location:');
        $element->setDescription('Enter the city and state/country:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('startsAt');
        $element->setLabel('From:');
        $element->setDescription('Dates in format YYYY-MM-DD.');
        $element->setRequired(true);
        $element->addValidator(new Zend_Validate_Date('YYYY-MM-DD'));
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('size', '10');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('endsAt');
        $element->setLabel('To:');
        $element->addValidator(new Zend_Validate_Date('YYYY-MM-DD'));
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('size', '10');
        $element->addValidator(new Application_Validate_EndDate());
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('url');
        $element->setLabel('URL:');
        $element->addValidator(new Application_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;'); // todo: move to css
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('body');
        $element->setLabel('Body:');
        $element->setAttrib('style', 'width:670px; height:15em'); // todo: move to css
        $this->addElement($element);
    }
}
