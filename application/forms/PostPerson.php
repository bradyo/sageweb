<?php

class Application_Form_PostPerson extends Application_Form_Abstract_Post
{
    public function init()
    {
        parent::init();

        $element = new Zend_Form_Element_Text('firstName');
        $element->setLabel('First Name:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('lastName');
        $element->setLabel('Last Name:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Email:');
        $element->addValidator(new Zend_Validate_EmailAddress());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('location');
        $element->setLabel('Location:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('personalUrl');
        $element->setLabel('Personal URL:');
        $element->addValidator(new My_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('labName');
        $element->setLabel('Lab Name:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:25em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('labUrl');
        $element->setLabel('Lab Url:');
        $element->addValidator(new My_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('body');
        $element->setLabel('Summary:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em; height:10em');
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Submit');
        $this->addElement($submitElement);
    }
}
