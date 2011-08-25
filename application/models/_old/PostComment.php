<?php

class Application_Form_PostComment extends Application_Form_Abstract_Post
{
    public function init()
    {
        parent::init();

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
}


