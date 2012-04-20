<?php

class Application_Form_Account extends Zend_Form
{
    public function init()
    {
        $this->setName('accountForm');
        $this->setMethod('post');

        $element = new Zend_Form_Element_Text('displayName');
        $element->setLabel('Display Name:');
        $element->setDescription('The display name is shown next to your posts. '
            . 'If left blank, your username will be shown.');
        $element->addFilter('StringTrim');
        $element->setAttrib('style', 'width:20em');
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('email');
        $element->setLabel('Primary E-mail');
        $element->setDescription(
            'All e-mails from the system will be sent to this address. '
            . 'The e-mail address is not made public and will only be used if '
            . 'you wish to receive a new password or wish to receive certain '
            . 'news or notifications by e-mail.'
        );
        $element->setRequired(true);
        $element->addValidator('EmailAddress');
        $element->setAttrib('style', 'width:25em');
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('timezone');
        $element->setLabel('Time Zone:');
        $timezoneOptions = array_combine(
            DateTimeZone::listIdentifiers(),
            DateTimeZone::listIdentifiers()
        );
        $element->setMultiOptions($timezoneOptions);
        $validator = new Zend_Validate_InArray($timezoneOptions);
        $element->addValidator($validator);
        $this->addElement($element);

        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Update');
        $this->addElement($element);
    }

    public function populate(array $values) {
        if (empty($values['displayName'])) {
            $values['displayName'] = $values['username'];
        }
        parent::populate($values);
    }
}