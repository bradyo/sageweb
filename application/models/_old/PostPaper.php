<?php

class Application_Form_PostPaper extends Application_Form_Abstract_Post
{
    public function init()
    {
        parent::init();

        $element = new Zend_Form_Element_Text('pubmedId');
        $element->setLabel('PubMedId:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('size', '10');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('authors');
        $element->setLabel('Authors:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('source');
        $element->setLabel('Source:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('publishDate');
        $element->setLabel('Publish Date:');
        $element->setDescription('Dates in format YYYY-MM-DD.');
        $element->addValidator(new Zend_Validate_Date('YYYY-MM-DD'));
        $element->setRequired(true);
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('size', '10');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('url');
        $element->setLabel('Permanent URL:');
        $element->setRequired(true);
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->addValidator(new Application_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('abstract');
        $element->setLabel('Abstract:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em; height:10em');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('summary');
        $element->setLabel('Summary:');
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em; height:10em');
        $this->addElement($element);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Submit');
        $this->addElement($submitElement);
    }
}
