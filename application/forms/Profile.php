<?php

class Application_Form_Profile extends Zend_Form
{
    public function init()
    {
        $this->setName('profileForm');
        $this->setMethod('post');

        $this->addElement('text', 'location', array(
            'label' => 'Location',
            'filters' => array('StringTrim'),
            'style' => 'width:20em',
        ));

        $this->addElement('text', 'websiteUrl', array(
            'label' => 'Website',
            'filters' => array('StringTrim'),
            'style' => 'width:20em',
        ));

        $this->addElement('text', 'blogUrl', array(
            'label' => 'Blog',
            'filters' => array('StringTrim'),
            'style' => 'width:20em',
        ));

        $this->addElement('text', 'blogRssUrl', array(
            'label' => 'Blog Feed',
            'filters' => array('StringTrim'),
            'style' => 'width:20em',
        ));

        $this->addElement('textarea', 'aboutBody', array(
            'label' => "About You",
            'filters' => array('StringTrim'),
            'style' => 'width:40em; height:8em',
        ));

        $this->addElement('textarea', 'interestsBody', array(
            'label' => "Interests",
            'filters' => array('StringTrim'),
            'style' => 'width:40em; height:4em',
        ));

        $emailElement = new Zend_Form_Element_Text('email');
        $emailElement->setLabel('Email:');
        $this->addElement($emailElement);

        $linkedInIdElement = new Zend_Form_Element_Text('linkedInId');
        $linkedInIdElement->setLabel('LinkedIn ID:');
        $this->addElement($linkedInIdElement);

        $facebookIdElement = new Zend_Form_Element_Text('facebookId');
        $facebookIdElement->setLabel('Facebook ID:');
        $this->addElement($facebookIdElement);

        $aimIdElement = new Zend_Form_Element_Text('aimId');
        $aimIdElement->setLabel('AIM ID:');
        $this->addElement($aimIdElement);

        $yahooIdElement = new Zend_Form_Element_Text('yahooId');
        $yahooIdElement->setLabel('Yahoo ID:');
        $this->addElement($yahooIdElement);

        $msnIdElement = new Zend_Form_Element_Text('msnId');
        $msnIdElement->setLabel('MSN ID:');
        $this->addElement($msnIdElement);

        $twitterIdElement = new Zend_Form_Element_Text('twitterId');
        $twitterIdElement->setLabel('Twitter ID:');
        $this->addElement($twitterIdElement);

       	$inTwitterStreamElement = new Zend_Form_Element_Checkbox('inTwitterStream');
        $inTwitterStreamElement->setLabel('Add your tweets to Sageweb stream?');
        $this->addElement($inTwitterStreamElement);

        // set decorator to view script
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'forms/profile.phtml'))
        ));
    }
}