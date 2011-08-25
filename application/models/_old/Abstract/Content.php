<?php

abstract class Application_Form_Abstract_Content extends Application_Form_Abstract_Post
{
    public function init()
    {
        parent::init();

        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_StringLength(array('min' => 10, 'max' => 128)));
        $element->setAttrib('style', 'width:670px');
        $this->addElement($element);

        $this->addElement('textarea', 'summary', array(
            'label' => "Summary:",
            'required' => false,
            'validators' => array(
                new Zend_Validate_StringLength(array(
                    'max' => 1000
                )),
            ),
            'style' => 'width:670px; height:4em',
        ));

        $element = new Zend_Form_Element_MultiCheckbox(
            'categories',
            array(
                'label' => 'Categories:',
                'multiOptions' => Sageweb_Table_Content::getDefaultCategoryOptions(),
                'decorators' => array('ViewHelper', 'Errors'),
                'separator' => '',
                'class' => 'categoryCheckbox'
            )
        );
        $this->addElement($element);

        if ($this->_viewingUser->isModerator()) {
            $featuredElement = new Zend_Form_Element_Checkbox('isFeatured');
            $featuredElement->setLabel('Is Featured:');
            $this->addElement($featuredElement);
        }
    }
}