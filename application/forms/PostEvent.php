<?php

class Application_Form_PostEvent extends Application_Form_Abstract_Content
{
    public function init()
    {
        parent::init();

        $element = new Zend_Form_Element_Select('type');
        $element->setLabel('Type:');
        $options = Sageweb_Cms_PostEvent::getTypeChoices();
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
        $element->addValidator(new My_Validate_EndDate());
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('url');
        $element->setLabel('URL:');
        $element->addValidator(new My_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('body');
        $element->setLabel('Body:');
        $element->setAttrib('style', 'width:670px; height:15em');
        $this->addElement($element);
    }
}
