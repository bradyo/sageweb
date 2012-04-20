<?php

class Application_Form_PostFile extends Application_Form_Abstract_Content
{
    private $_upload = null;

    public function __construct($options = array())
    {
        if (isset($options['upload'])) {
            $this->_upload = $options['upload'];
            unset($options['upload']);
        }
        parent::__construct($options);
    }

    public function init()
    {
        parent::init();
        $this->setName('fileForm');
        $this->setAttrib('enctype', 'multipart/form-data');

        $element = $this->getElement('categories');
        $element->setMultiOptions(
            Sageweb_Cms_Table_Content::getDefaultCategoryOptions() +
            Sageweb_Cms_Table_Content::getMediaCategoryOptions()
        );

        $element = new Zend_Form_Element_File('file');
        $element->setLabel('Select File:');
        
        if (!$this->hasUpload()) {    
            $element->setRequired(true);
            $element->addValidator(new Zend_Validate_NotEmpty());
        }
        $element->setMaxFileSize(-1);
        $element->addValidator('Count', false, 1);   
        $element->setDecorators(array('File', 'Errors'));
			  $this->addElement($element);
    }

    public function hasUpload()
    {
        return ($this->_upload !== null);
    }

    public function getUpload()
    {
        return $this->_upload;
    }
}
