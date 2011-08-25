<?php

class Application_Form_Flag extends Zend_Form
{
    public function init()
    {
        $element = new Zend_Form_Element_Hidden('entityId');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('type');
        $element->setLabel('Type');
        $element->setRequired(true);
        $options = Application_Model_Entity_EntityFlag::getTypeChoices();
        $element->setMultiOptions($options);
        $element->addValidator(new Zend_Validate_InArray(array_keys($options)));
        $this->addElement($element);

        $element = new Zend_Form_Element_Textarea('comment');
        $element->setLabel('Comment');
        $element->setAttrib('rows', '3');
        $element->setAttrib('cols', '40');
        $element->setAttrib('style', 'width:98%');
        $this->addElement($element);
    }
}


