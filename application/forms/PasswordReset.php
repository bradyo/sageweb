<?php

class Application_Form_PasswordReset extends Zend_Form
{
    public function init()
    {
        $this->setName('resetForm');
        $this->setMethod('post');

        $this->addElement('text', 'username', array(
            'label' => "Username",
            'required' => true,
            'style' => 'width:20em',
            'validators' => array(
                new Zend_Validate_NotEmpty(),
                new Sageweb_Validate_Identity()
            )
        ));

        // set decorator to view script
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'forms/reset.phtml'))
        ));
    }
}