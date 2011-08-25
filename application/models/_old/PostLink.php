<?php

class Application_Form_PostLink extends Application_Form_Abstract_Content
{
    public function init()
    {
        parent::init();
        $this->setName('linkForm');

        $element = $this->getElement('categories');
        $element->setMultiOptions(
            Sageweb_Table_Content::getDefaultCategoryOptions() +
            Sageweb_Table_Content::getMediaCategoryOptions() +
            Sageweb_Table_Content::getLinkCategoryOptions()
        );

        $element = new Zend_Form_Element_Text('url');
        $element->setLabel('URL:');
        $element->setRequired(true);
        $element->addValidator(new Zend_Validate_NotEmpty());
        $element->addValidator(new Application_Validate_Uri());
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setAttrib('style', 'width:50em;');
        $this->addElement($element);
    }
}
