<?php

class Application_Model_User_RegisterForm extends Zend_Form
{
    public function init()
    {
        $this->setName('registerForm');
        $this->setMethod('post');

        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'required' => true,
            'validators' => array(
                new Zend_Validate_Alnum(false),
                new Zend_Validate_StringLength(array(
                    'min' => 3,
                    'max' => 32
                )),
            ),
            'filters' => array('StringTrim'),
            'style' => 'width:20em',
        ));

        $this->addElement('text', 'email', array(
            'label' => "E-mail",
            'required' => true,
            'validators' => array(
                new Zend_Validate_EmailAddress(),
            ),
            'style' => 'width:20em',
        ));

        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Login',
        ));

        // Trying a "dumb capcha" to prevent spam bots from posting. Recaptcha
        // failed to prevent spam from bots that cracked Recaptcha.
        $element = new Zend_Form_Element_Text('captcha');
        $element->setLabel('Enter "sageweb" below:');
        $element->setRequired(true);
        $element->addValidator(new Application_Validate_Captcha());
        $this->addElement($element);

        // This captcha is commented out since it was not blocking spam very well
        // 
//        $captchaElement = new Zend_Form_Element_Captcha('captcha',
//            array(
//                'label' => 'Enter the words below:',
//                'captcha' =>  'ReCaptcha',
//                'captchaOptions' => array(
//                    'captcha' => 'ReCaptcha',
//                    'service' => new Application_Service_ReCaptchaService(),
//                    'theme' => 'clean'
//                )
//            )
//        );
//        $this->addElement($captchaElement);
    }
}