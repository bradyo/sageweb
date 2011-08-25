<?php

class Application_Model_Comment_CommentForm extends Zend_Form
{
    public function init()
    {
        $this->setMethod(Zend_Form::METHOD_POST);
        $this->setAction('');

        $this->addElement('textarea', 'body', array(
            'label'      => 'Comment:',
            'required'   => true,
            'rows'       => 3,
            'cols'       => 40,
            'style' => 'width: 98%; ',
        ));

        $this->addElement('submit', 'Submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
            'style'     => 'width:10em'
        ));
    }

    public function isValid($data)
    {
        // check entities values

        return parent::isValid($data);
    }
}


