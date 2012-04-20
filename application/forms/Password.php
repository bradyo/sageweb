<?php

class Application_Form_Password extends Zend_Form
{
    public function init()
    {
        $this->setName('passwordForm');
        $this->setMethod('post');

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'description' => 'Enter at least 5 characters.',
            'style' => 'width:12em',
            'validators' => array(
                new Zend_Validate_StringLength(array('min' => '4')),
                new Zend_Validate_Identical('passwordConfirm'),
            ),
            'required' => true,
        ));
        $this->addElement('password', 'passwordConfirm', array(
            'label' => 'Confirm password',
            'style' => 'width:12em',
        ));

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Update');
        $this->addElement($element);
    }
}