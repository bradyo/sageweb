<?php

class Application_Form_PostArticle extends Application_Form_Abstract_Content
{
    public function init()
    {
        parent::init();
        $this->setName('articleForm');

        $element = new Zend_Form_Element_Textarea('body');
        $element->setLabel('Body:');
        $element->setRequired(true);
        $element->addValidator('NotEmpty');
        $element->setAttrib('style', 'width:670px; height:15em');
        $this->addElement($element);
    }
}
