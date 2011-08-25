<?php

class Application_Form_PostLab extends Application_Form_Abstract_Post
{
    public function init()
    {
        parent::init();

        $element = new Zend_Form_Element_Text('name');
        $element->setLabel('Name:');
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('type');
        $element->setLabel('Type:');
        $options = Sageweb_PostLab::getTypeChoices();
        $element->setMultiOptions($options);
        $element->addValidator(new Zend_Validate_InArray(array_keys($options)));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('location');
        $element->setLabel('Location:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('url');
        $element->setLabel('URL:');
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->addValidator(new Application_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('body');
        $element->setLabel('Description:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em; height:10em');
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Submit');
        $this->addElement($submitElement);
    }
}
