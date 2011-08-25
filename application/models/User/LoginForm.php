<?php
class Application_Model_User_LoginForm extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $this->addElement('text', 'username', array(
                'label' => 'Username',
                'required' => true,
                'filters' => array('StringTrim'),
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
        ));

        $this->addElement('checkbox', 'remember', array(
            'label' => 'Remember Me',
        ));
    }
}