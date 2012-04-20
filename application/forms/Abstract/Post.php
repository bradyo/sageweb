<?php

abstract class Application_Form_Abstract_Post extends Zend_Form
{
    /** @var Sageweb_Cms_User $_viewingUser */
    protected $_viewingUser = null;

    public function __construct($options = array())
    {
        if (isset($options['viewingUser'])) {
            $this->_viewingUser = $options['viewingUser'];
            unset($options['viewingUser']);
        }
        parent::__construct($options);
    }

    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'utf-8');

        $idElement = new Zend_Form_Element_Hidden('id');
        $this->addElement($idElement);

        // tags element
        $element = new Zend_Form_Element_Text('tags');
        $element->setLabel('Tags:');
        $element->addValidator('StringLength', array('min' => 10, 'max' => '128'));
        $element->setAttrib('style', 'width:670px');
        $this->addElement($element);

        // captcha element for non-moderators only
        if ($this->_viewingUser && !$this->_viewingUser->isModerator() && APPLICATION_ENV !== 'ddevelopment') {

            $element = new Zend_Form_Element_Text('captcha');
            $element->setLabel('Enter "sageweb" below:');
            $element->setRequired(true);
            $element->addValidator(new Sageweb_Validate_Captcha());
            $this->addElement($element);

//            $captchaElement = new Zend_Form_Element_Captcha('captcha',
//                array(
//                    'label' => 'Enter the words below:',
//                    'captcha' =>  'ReCaptcha',
//                    'captchaOptions' => array(
//                        'captcha' => 'ReCaptcha',
//                        'service' => new Application_Service_ReCaptchaService(),
//                        'theme' => 'clean'
//                    )
//                )
//            );
//            $this->addElement($captchaElement);
        }

        // moderator elements
        if ($this->_viewingUser && $this->_viewingUser->isModerator()) {
            $element = new Zend_Form_Element_Select('status');
            $element->setLabel('Status:');
            $options = Sageweb_Cms_Abstract_Post::getStatusOptions();
            $element->setMultiOptions($options);
            $element->addValidator(new Zend_Validate_InArray(array_keys($options)));
            $this->addElement($element);

            $authorElement = new Zend_Form_Element_Text('author');
            $authorElement->setLabel('Author:');
            $authorElement->addValidator(new Sageweb_Validate_Username());
            $this->addElement($authorElement);
        }

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Submit');
        $this->addElement($submitElement);
    }

    public function showModerationFields()
    {
        if (!$this->_viewingUser) {
            return false;
        }
        return $this->_viewingUser->isModerator();
    }

    public function populate(array $values)
    {
        // convert tags to string
        if (isset($values['categories'])) {
            $values['categories'] = $this->_getCategoryValues($values['categories']);
        }
        if (isset($values['tags'])) {
            $values['tags'] = $this->_getTagValues($values['tags']);
        }

        // get author username
        if ($this->_viewingUser && $this->_viewingUser->isModerator()) {
            $author = Sageweb_Cms_Table_User::findOneById($values['authorId']);
            $values['author'] = $author->username;
        }
        parent::populate($values);
    }

    protected function _getCategoryValues($items)
    {
        $values = array();
        foreach ($items as $item) {
            $values[] = $item['value'];
        }
        return $values;
    }

    protected function _getTagValues($items)
    {
        $values = array();
        foreach ($items as $item) {
            $values[] = $item['value'];
        }
        return join(', ', $values);
    }
}