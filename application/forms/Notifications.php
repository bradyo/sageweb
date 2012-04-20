<?php

class Application_Form_Notifications extends Zend_Form
{
    public function init()
    {
        $this->setName('notificationsForm');
        $this->setMethod('post');

        $element = new Zend_Form_Element_Select('newsletter');
        $element->setLabel('New Content Newsletter:');
        $choices = array(
            'none' => 'Never',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly'
        );
        $element->setMultiOptions($choices);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Update');
        $this->addElement($element);
    }
}