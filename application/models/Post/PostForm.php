<?php

abstract class Application_Model_Post_PostForm extends Zend_Form {
    
    protected $categoryOptions;
    
    /**
     * @var Application_Model_User_User 
     */
    protected $viewingUser = null;

    public function __construct(Application_Model_User_User $viewingUser, $options = array()) {
        $this->viewingUser = $viewingUser;
    }
    
    public function __construct($options = array()) {
        if (isset($options['viewingUser'])) {
            $this->viewingUser = $options['viewingUser'];
            unset($options['viewingUser']);
        }
        if (isset($options['categoryOptions'])) {
            $this->categoryOptions = $options['categoryOptions'];
            unset($options['categoryOptions']);
        }
        parent::__construct($options);
    }

    public function init() {
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'utf-8');

        // id element
        $idElement = new Zend_Form_Element_Hidden('id');
        $this->addElement($idElement);

        // title element
        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $element->addFilter(new Zend_Filter_StringTrim());
        $element->addValidator(new Zend_Validate_StringLength(array('min' => 10, 'max' => 128)));
        $element->setAttrib('style', 'width:670px'); // TODO move to css
        $this->addElement($element);
        
        // summary element
        $element = new Zend_Form_Element_Textarea('summary');
        $element->setLabel('Summary:');
        $validator = new Zend_Validate_StringLength(array('max' => 1000));
        $element->addValidator($validator);
        $element->setAttrib('style', 'width:670px; height:4em');
        $this->addElement($element);

        // categories element
        $element = new Zend_Form_Element_Multi('categories');
        $element->setLabel('Categories:');
        $element->setMultiOptions($this->categoryOptions);
        $element->setDecorators(array('ViewHelper', 'Errors'));
        $element->setSeparator('');
        $element->setAttrib('class', 'categoryCheckbox');
        $this->addElement($element);

        // tags element
        $element = new Zend_Form_Element_Text('tags');
        $element->setLabel('Tags:');
        $element->addValidator('StringLength', array('min' => 10, 'max' => '128'));
        $element->setAttrib('style', 'width:670px'); // TODO: move to css
        $this->addElement($element);
        
        // moderator elements
        if ($this->showModerationFields()) {
            $element = new Zend_Form_Element_Select('status');
            $element->setLabel('Status:');
            $options = $this->statusOptions;
            $element->setMultiOptions($options);
            $validator = new Zend_Validate_InArray(array_keys($options));
            $element->addValidator($validator);
            $this->addElement($element);

            $element = new Zend_Form_Element_Text('author');
            $element->setLabel('Author:');
            $element->addValidator(new Application_Validate_Username());
            $this->addElement($element);
            
            $element = new Zend_Form_Element_Checkbox('isFeatured');
            $element->setLabel('Is Featured:');
            $this->addElement($featuredElement);
        }
        
        // captcha element for non-moderators only
        if ($this->viewingUser === null || ! $this->viewingUser->isModerator()) {
            $element = new Zend_Form_Element_Text('captcha');
            $element->setLabel('Enter "sageweb" below:');
            $element->setRequired(true);
            $element->addValidator(new Application_Validate_Captcha());
            $this->addElement($element);
        }

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Submit');
        $this->addElement($submitElement);
    }

    public function populate(array $values) {
        // convert tags to string
        if (isset($values['categories'])) {
            $values['categories'] = $this->getCategoryValues($values['categories']);
        }
        if (isset($values['tags'])) {
            $values['tags'] = $this->getTagValues($values['tags']);
        }

        // get author username
        if ($this->showModerationFields()) {
            $author = Sageweb_Table_User::findOneById($values['authorId']);
            $values['author'] = $author->username;
        }
        parent::populate($values);
    }
    
    public function showModerationFields() {
        return ($this->viewingUser !== null && $this->viewingUser->isModerator());
    }

    protected function getCategoryValues($items) {
        $values = array();
        foreach ($items as $item) {
            $values[] = $item['value'];
        }
        return $values;
    }

    protected function getTagValues($items) {
        $values = array();
        foreach ($items as $item) {
            $values[] = $item['value'];
        }
        return join(', ', $values);
    }
}