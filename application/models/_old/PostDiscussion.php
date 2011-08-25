<?php

class Application_Form_PostDiscussion extends Application_Form_Abstract_Post
{
    public function init()
    {
        parent::init();

        $element = new Zend_Form_Element_Select('forumId');
        $element->setLabel('Forum:');
        $options = Sageweb_Table_Forum::getForumChoices();
        $element->setMultiOptions($options);
        $element->setRequired(true);
        $element->addValidator(new Zend_Validate_InArray(array_keys($options)));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('body');
        $element->setLabel('Body:');
        $element->setRequired(true);
        $element->addValidator('NotEmpty');
        $element->setAttrib('style', 'width:670px; height:15em');
        $this->addElement($element);
    }

}
